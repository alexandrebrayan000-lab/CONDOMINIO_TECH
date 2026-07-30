document.addEventListener('DOMContentLoaded', function(){
    const bookingModal = document.getElementById('bookingModal');
    if (!bookingModal) return; // não executar neste documento
    const closeModalButton = document.getElementById('closeModal');
    const bookingForm = document.getElementById('bookingForm');
    const resourceName = document.getElementById('resourceName');
    const bookingFeedback = document.getElementById('bookingFeedback');
    const bookingDate = document.getElementById('bookingDate');
    const bookingInterval = document.getElementById('bookingInterval');
    const bookingTime = document.getElementById('bookingTime');
    const bookingPeople = document.getElementById('bookingPeople');

    const STORAGE_KEY = 'reservasReservAtiva';

function setMinimumBookingDate() {
    bookingDate.min = new Date().toISOString().split('T')[0];
}

function formatTime(minutes) {
    const hours = String(Math.floor(minutes / 60)).padStart(2, '0');
    const mins = String(minutes % 60).padStart(2, '0');
    return `${hours}:${mins}`;
}

function populateTimeOptions(stepMinutes) {
    bookingTime.innerHTML = '';
    const startMinutes = 8 * 60;  // 08:00
    const endMinutes = 22 * 60;   // 22:00

    for (let minutes = startMinutes; minutes <= endMinutes; minutes += stepMinutes) {
        const option = document.createElement('option');
        option.value = formatTime(minutes);
        option.textContent = formatTime(minutes);
        bookingTime.appendChild(option);
    }
}

function openModal(resource) {
    resourceName.textContent = resource;
    bookingFeedback.textContent = '';
    bookingFeedback.className = 'booking-feedback';
    bookingForm.reset();
    setMinimumBookingDate();
    populateTimeOptions(Number(bookingInterval.value));
    bookingModal.classList.add('active');
    bookingModal.setAttribute('aria-hidden', 'false');
    bookingDate.focus();
}

function closeBookingModal() {
    bookingModal.classList.remove('active');
    bookingModal.setAttribute('aria-hidden', 'true');
}

function saveBooking(booking) {
    const stored = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    stored.push(booking);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(stored));
}

// ENVIO INTELIGENTE AO BANCO DE DADOS
function handleBookingSubmit(event) {
    event.preventDefault();

    const dadosFormulario = new FormData();
    dadosFormulario.append('resource', resourceName.textContent);
    dadosFormulario.append('date', bookingDate.value);
    dadosFormulario.append('time', bookingTime.value);
    dadosFormulario.append('interval', bookingInterval.value);
    dadosFormulario.append('people', bookingPeople.value);
    dadosFormulario.append('name', document.getElementById('bookingName').value);

    bookingFeedback.textContent = 'Verificando horários no servidor...';
    bookingFeedback.className = 'booking-feedback';

    // Dispara a validação para o PHP
    fetch('conectar.php', {
        method: 'POST',
        body: dadosFormulario
    })
    .then(resposta => resposta.json())
    .then(dados => {
        if (dados.status === 'sucesso') {
            bookingFeedback.textContent = '🎉 ' + dados.mensagem;
            bookingFeedback.className = 'booking-feedback success';
            bookingForm.reset();
            
            // Salva no LocalStorage também por segurança histórica
            saveBooking({
                resource: resourceName.textContent,
                date: bookingDate.value,
                time: bookingTime.value,
                people: bookingPeople.value,
                name: document.getElementById('bookingName').value,
                createdAt: new Date().toISOString()
            });
        } else {
            // Exibe o erro de conflito de horários direto no modal
            bookingFeedback.textContent = '❌ ' + dados.mensagem;
            bookingFeedback.className = 'booking-feedback error';
        }
    })
    .catch(erro => {
        console.error('Erro:', erro);
        bookingFeedback.textContent = '❌ Erro de ligação com o banco de dados MySQL.';
        bookingFeedback.className = 'booking-feedback error';
    });
}

function initReservaModal() {
    const reservaItems = document.querySelectorAll('.reserva-item');

    reservaItems.forEach((item) => {
        item.addEventListener('click', () => {
            openModal(item.dataset.resource);
        });
    });

    closeModalButton.addEventListener('click', closeBookingModal);

    bookingModal.addEventListener('click', (event) => {
        if (event.target === bookingModal) {
            closeBookingModal();
        }
    });

    bookingInterval.addEventListener('change', () => {
        populateTimeOptions(Number(bookingInterval.value));
    });

    bookingForm.addEventListener('submit', handleBookingSubmit);
    }

    initReservaModal();
});