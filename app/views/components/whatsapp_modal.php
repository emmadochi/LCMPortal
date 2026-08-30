<!-- WhatsApp 1-Click Follow-Up Message Modal -->
<div class="modal fade" id="whatsappFollowUpModal" tabindex="-1" aria-labelledby="whatsappFollowUpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                <div class="d-flex align-items-center">
                    <div class="avatar-xs rounded-circle bg-white text-success d-flex align-items-center justify-content-center me-2 font-size-18">
                        <i class="bx bxl-whatsapp"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="whatsappFollowUpModalLabel">1-Click WhatsApp Follow-Up</h5>
                        <small class="text-white-50 font-size-12">Select a pre-composed template or customize your message</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Recipient Info Banner -->
                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mb-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-3 fw-bold font-size-14" id="waModalAvatar">
                            C
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark font-size-14" id="waModalConvertName">Convert Name</h6>
                            <small class="text-muted font-size-12" id="waModalConvertPhone">08012345678</small>
                        </div>
                    </div>
                    <span class="badge bg-success-subtle text-success font-size-11 px-2.5 py-1.5 rounded-pill">
                        <i class="bx bx-check-circle me-1"></i> WhatsApp Ready
                    </span>
                </div>

                <!-- Template Selector Pills -->
                <div class="mb-3">
                    <label class="form-label fw-bold font-size-13 text-dark mb-2">
                        <i class="bx bx-select-multiple text-success me-1"></i> Choose a Message Template:
                    </label>
                    <div class="row g-2" id="waTemplateGrid">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border wa-template-card cursor-pointer h-100 transition-all active-template" 
                                 data-type="welcome" style="border: 2px solid #059669 !important; background: #ecfdf5;">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-success text-white font-size-11 me-2">Welcome</span>
                                    <strong class="font-size-13 text-dark">Accepting Christ & Gratitude</strong>
                                </div>
                                <p class="text-muted font-size-12 mb-0 line-clamp-2">Rejoicing with you on your decision to accept Jesus Christ...</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border wa-template-card cursor-pointer h-100 transition-all" 
                                 data-type="sunday" style="background: #f8fafc;">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-primary text-white font-size-11 me-2">Church</span>
                                    <strong class="font-size-13 text-dark">Sunday Worship Invite</strong>
                                </div>
                                <p class="text-muted font-size-12 mb-0 line-clamp-2">Warmly inviting you to worship with us this Sunday...</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border wa-template-card cursor-pointer h-100 transition-all" 
                                 data-type="foundation" style="background: #f8fafc;">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-warning text-dark font-size-11 me-2">Growth</span>
                                    <strong class="font-size-13 text-dark">Foundation School Class</strong>
                                </div>
                                <p class="text-muted font-size-12 mb-0 line-clamp-2">Join our Christian growth & Bible foundation class...</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border wa-template-card cursor-pointer h-100 transition-all" 
                                 data-type="prayer" style="background: #f8fafc;">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-info text-white font-size-11 me-2">Care</span>
                                    <strong class="font-size-13 text-dark">Prayer & Family Check-in</strong>
                                </div>
                                <p class="text-muted font-size-12 mb-0 line-clamp-2">Checking in on you to pray for you and your family...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editable Message Preview Textarea -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="waMessageText" class="form-label fw-semibold font-size-13 text-dark mb-0">
                            Message Preview (You can edit before sending):
                        </label>
                        <small class="text-muted font-size-11"><span id="waCharCount">0</span> characters</small>
                    </div>
                    <textarea id="waMessageText" class="form-control rounded-3 p-3 font-size-13" rows="5" style="border: 1px solid #cbd5e1; line-height: 1.6;"></textarea>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success fw-bold rounded-pill px-5 shadow-sm d-flex align-items-center" id="waSendBtn" onclick="dispatchWhatsAppMessage()">
                    <i class="bx bxl-whatsapp font-size-18 me-1.5"></i> Open WhatsApp & Send
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer { cursor: pointer; }
.transition-all { transition: all 0.2s ease-in-out; }
.wa-template-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
let currentWaConvert = {
    name: '',
    firstName: '',
    phone: '',
    cleanPhone: ''
};

const waTemplates = {
    welcome: (name, firstName) => `Hello ${firstName}! 🌟\n\nIt was such a great blessing meeting you and rejoicing with you as you accepted Jesus Christ! We at Life Changers Touch Church are constantly praying for God's grace, peace, and abundance over your life and family.\n\nHow has your week been so far? Please let us know how we can pray for you today! 🙏`,
    sunday: (name, firstName) => `Hello ${firstName}! ⛪\n\nWe warmly invite you to worship with us this Sunday at Life Changers Touch Church! Service starts at 8:00 AM.\n\nWe have a special seat reserved for you, and we would truly love to fellowship and celebrate God's goodness together. Let me know if you need any directions! See you Sunday! 😊`,
    foundation: (name, firstName) => `Hello ${firstName}! 📖\n\nTrust you are having a wonderful and blessed week!\n\nOur Christian Growth Foundation Class is holding this week to help you grow strong in your spiritual walk, understand God's Word, and discover His glorious purpose for your life. Would you like us to save a spot for you?`,
    prayer: (name, firstName) => `Hello ${firstName}! 🙏\n\nJust checking in on you from Life Changers Touch Church! We have been lifting you up in our prayers.\n\nIs there any specific prayer request or need on your heart that we can stand in faith and agreement with you for today? God bless you richly!`
};

function openWhatsAppTemplateModal(name, phone) {
    if (!phone) {
        alert('No phone number provided for this convert.');
        return;
    }

    const cleanPhone = phone.replace(/[^0-9]/g, '');
    const firstName = (name || 'Friend').trim().split(' ')[0];

    currentWaConvert = {
        name: name || 'Convert',
        firstName: firstName,
        phone: phone,
        cleanPhone: cleanPhone
    };

    document.getElementById('waModalConvertName').textContent = currentWaConvert.name;
    document.getElementById('waModalConvertPhone').textContent = currentWaConvert.phone;
    document.getElementById('waModalAvatar').textContent = (currentWaConvert.name[0] || 'C').toUpperCase();

    // Set active card & load welcome template
    document.querySelectorAll('.wa-template-card').forEach(c => {
        c.style.border = '1px solid #e2e8f0';
        c.style.background = '#f8fafc';
    });
    const welcomeCard = document.querySelector('.wa-template-card[data-type="welcome"]');
    if (welcomeCard) {
        welcomeCard.style.border = '2px solid #059669 !important';
        welcomeCard.style.background = '#ecfdf5';
    }

    const msg = waTemplates.welcome(currentWaConvert.name, currentWaConvert.firstName);
    const textarea = document.getElementById('waMessageText');
    textarea.value = msg;
    document.getElementById('waCharCount').textContent = msg.length;

    const modalEl = document.getElementById('whatsappFollowUpModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Template click handler
    document.querySelectorAll('.wa-template-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.wa-template-card').forEach(c => {
                c.style.border = '1px solid #e2e8f0';
                c.style.background = '#f8fafc';
            });
            this.style.border = '2px solid #059669';
            this.style.background = '#ecfdf5';

            const type = this.getAttribute('data-type');
            if (waTemplates[type]) {
                const msg = waTemplates[type](currentWaConvert.name, currentWaConvert.firstName);
                const textarea = document.getElementById('waMessageText');
                textarea.value = msg;
                document.getElementById('waCharCount').textContent = msg.length;
            }
        });
    });

    const textarea = document.getElementById('waMessageText');
    if (textarea) {
        textarea.addEventListener('input', function() {
            document.getElementById('waCharCount').textContent = this.value.length;
        });
    }
});

function dispatchWhatsAppMessage() {
    const text = document.getElementById('waMessageText').value;
    if (!currentWaConvert.cleanPhone) {
        alert('Invalid phone number.');
        return;
    }
    const url = `https://wa.me/${currentWaConvert.cleanPhone}?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');

    // Close modal
    const modalEl = document.getElementById('whatsappFollowUpModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
}
</script>
