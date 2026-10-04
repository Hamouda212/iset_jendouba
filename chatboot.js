// chatbot.js - Intelligent Chatbot Assistant for ISET Jendouba Events Portal

class ISETChatbot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.userName = null;
        this.context = {
            currentPage: this.getCurrentPage(),
            lastIntent: null,
            waitingFor: null
        };
        
        this.init();
    }
    
    getCurrentPage() {
        const path = window.location.pathname;
        if (path.includes('adminevent.php')) return 'admin';
        if (path.includes('events.php')) return 'events';
        if (path.includes('notifications.php')) return 'notifications';
        if (path.includes('profile.php')) return 'profile';
        if (path.includes('login.php')) return 'login';
        if (path.includes('register.php')) return 'register';
        return 'home';
    }
    
    init() {
        this.createChatbotUI();
        this.loadMessages();
        this.addWelcomeMessage();
    }
    
    createChatbotUI() {
        // Create chatbot container
        const chatbotHTML = `
            <div id="chatbot-container" class="chatbot-container">
                <div class="chatbot-toggle" id="chatbot-toggle">
                    <div class="chatbot-icon">
                        <i class="fas fa-comment-dots"></i>
                        <span class="notification-badge" id="chatbot-notif" style="display: none;">1</span>
                    </div>
                    <div class="chatbot-toggle-text">Need Help?</div>
                </div>
                
                <div class="chatbot-window" id="chatbot-window">
                    <div class="chatbot-header">
                        <div class="chatbot-header-info">
                            <div class="chatbot-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div>
                                <h3>ISET Assistant</h3>
                                <p>Online • Ready to help</p>
                            </div>
                        </div>
                        <button class="chatbot-minimize" id="chatbot-minimize">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="chatbot-messages" id="chatbot-messages">
                        <!-- Messages will appear here -->
                    </div>
                    
                    <div class="chatbot-typing" id="chatbot-typing" style="display: none;">
                        <span>ISET Assistant is typing</span>
                        <span class="typing-dots">...</span>
                    </div>
                    
                    <div class="chatbot-input-area">
                        <div class="quick-actions" id="quick-actions">
                            <button class="quick-btn" data-action="events">📅 View Events</button>
                            <button class="quick-btn" data-action="register">📝 Register</button>
                            <button class="quick-btn" data-action="notifications">🔔 Notifications</button>
                            <button class="quick-btn" data-action="help">❓ Help</button>
                        </div>
                        <div class="chatbot-input-wrapper">
                            <input type="text" id="chatbot-input" placeholder="Ask me anything..." autocomplete="off">
                            <button id="chatbot-send" class="chatbot-send-btn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', chatbotHTML);
        
        // Add CSS
        this.addStyles();
        
        // Bind events
        this.bindEvents();
    }
    
    addStyles() {
        const styles = `
            <style>
                .chatbot-container {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    z-index: 10000;
                    font-family: 'Poppins', sans-serif;
                }
                
                .chatbot-toggle {
                    background: linear-gradient(135deg, #FFD700 0%, #e6c200 100%);
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                    transition: all 0.3s ease;
                    position: relative;
                }
                
                .chatbot-toggle:hover {
                    transform: scale(1.05);
                    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
                }
                
                .chatbot-icon {
                    position: relative;
                }
                
                .chatbot-icon i {
                    font-size: 28px;
                    color: #1a1a2e;
                }
                
                .chatbot-toggle-text {
                    position: absolute;
                    right: 70px;
                    background: #1a1a2e;
                    color: white;
                    padding: 5px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    white-space: nowrap;
                    opacity: 0;
                    transition: opacity 0.3s;
                    pointer-events: none;
                }
                
                .chatbot-toggle:hover .chatbot-toggle-text {
                    opacity: 1;
                }
                
                .notification-badge {
                    position: absolute;
                    top: -8px;
                    right: -8px;
                    background: #dc3545;
                    color: white;
                    border-radius: 50%;
                    width: 18px;
                    height: 18px;
                    font-size: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .chatbot-window {
                    position: absolute;
                    bottom: 80px;
                    right: 0;
                    width: 380px;
                    height: 550px;
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    display: none;
                    flex-direction: column;
                    overflow: hidden;
                    animation: slideUp 0.3s ease;
                }
                
                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                .chatbot-window.open {
                    display: flex;
                }
                
                .chatbot-header {
                    background: #1a1a2e;
                    color: white;
                    padding: 15px 20px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .chatbot-header-info {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }
                
                .chatbot-avatar {
                    width: 40px;
                    height: 40px;
                    background: #FFD700;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .chatbot-avatar i {
                    font-size: 22px;
                    color: #1a1a2e;
                }
                
                .chatbot-header h3 {
                    font-size: 16px;
                    margin: 0;
                }
                
                .chatbot-header p {
                    font-size: 11px;
                    margin: 0;
                    opacity: 0.8;
                }
                
                .chatbot-minimize {
                    background: none;
                    border: none;
                    color: white;
                    font-size: 20px;
                    cursor: pointer;
                    padding: 5px;
                }
                
                .chatbot-messages {
                    flex: 1;
                    padding: 20px;
                    overflow-y: auto;
                    background: #f8f9fa;
                }
                
                .message {
                    margin-bottom: 15px;
                    display: flex;
                    animation: fadeIn 0.3s ease;
                }
                
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                .message.user {
                    justify-content: flex-end;
                }
                
                .message.bot {
                    justify-content: flex-start;
                }
                
                .message-content {
                    max-width: 80%;
                    padding: 10px 15px;
                    border-radius: 18px;
                    font-size: 14px;
                    line-height: 1.4;
                }
                
                .message.user .message-content {
                    background: #FFD700;
                    color: #1a1a2e;
                    border-bottom-right-radius: 4px;
                }
                
                .message.bot .message-content {
                    background: white;
                    color: #333;
                    border-bottom-left-radius: 4px;
                    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
                }
                
                .chatbot-typing {
                    padding: 10px 20px;
                    font-size: 12px;
                    color: #666;
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }
                
                .typing-dots {
                    animation: typing 1.5s infinite;
                }
                
                @keyframes typing {
                    0%, 20% { opacity: 0.3; }
                    50% { opacity: 1; }
                    80%, 100% { opacity: 0.3; }
                }
                
                .chatbot-input-area {
                    padding: 15px 20px;
                    background: white;
                    border-top: 1px solid #e9ecef;
                }
                
                .quick-actions {
                    display: flex;
                    gap: 8px;
                    margin-bottom: 12px;
                    flex-wrap: wrap;
                }
                
                .quick-btn {
                    background: #f0f2f5;
                    border: none;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 11px;
                    cursor: pointer;
                    transition: all 0.2s;
                    font-family: inherit;
                }
                
                .quick-btn:hover {
                    background: #FFD700;
                    transform: translateY(-1px);
                }
                
                .chatbot-input-wrapper {
                    display: flex;
                    gap: 10px;
                }
                
                #chatbot-input {
                    flex: 1;
                    padding: 10px 15px;
                    border: 2px solid #e9ecef;
                    border-radius: 25px;
                    font-family: inherit;
                    font-size: 14px;
                    outline: none;
                    transition: all 0.2s;
                }
                
                #chatbot-input:focus {
                    border-color: #FFD700;
                }
                
                .chatbot-send-btn {
                    background: #FFD700;
                    border: none;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    cursor: pointer;
                    transition: all 0.2s;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .chatbot-send-btn:hover {
                    background: #e6c200;
                    transform: scale(1.05);
                }
                
                .chatbot-send-btn i {
                    color: #1a1a2e;
                }
                
                @media (max-width: 480px) {
                    .chatbot-window {
                        width: calc(100vw - 40px);
                        right: 0;
                        height: 500px;
                    }
                }
            </style>
        `;
        
        document.head.insertAdjacentHTML('beforeend', styles);
    }
    
    bindEvents() {
        const toggle = document.getElementById('chatbot-toggle');
        const window = document.getElementById('chatbot-window');
        const minimize = document.getElementById('chatbot-minimize');
        const sendBtn = document.getElementById('chatbot-send');
        const input = document.getElementById('chatbot-input');
        
        toggle.addEventListener('click', () => this.toggleChatbot());
        minimize.addEventListener('click', () => this.closeChatbot());
        sendBtn.addEventListener('click', () => this.sendMessage());
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
        
        // Quick actions
        document.querySelectorAll('.quick-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.dataset.action;
                this.handleQuickAction(action);
            });
        });
        
        // Close on outside click (optional)
        document.addEventListener('click', (e) => {
            if (this.isOpen && !window.contains(e.target) && !toggle.contains(e.target)) {
                this.closeChatbot();
            }
        });
    }
    
    toggleChatbot() {
        const window = document.getElementById('chatbot-window');
        if (this.isOpen) {
            this.closeChatbot();
        } else {
            this.openChatbot();
        }
    }
    
    openChatbot() {
        const window = document.getElementById('chatbot-window');
        window.classList.add('open');
        this.isOpen = true;
        
        // Hide notification badge
        const notif = document.getElementById('chatbot-notif');
        if (notif) notif.style.display = 'none';
        
        // Scroll to bottom
        this.scrollToBottom();
    }
    
    closeChatbot() {
        const window = document.getElementById('chatbot-window');
        window.classList.remove('open');
        this.isOpen = false;
    }
    
    addWelcomeMessage() {
        setTimeout(() => {
            let welcomeMsg = "Hello! 👋 I'm your ISET Jendouba assistant. How can I help you today?";
            
            // Personalized welcome based on page
            switch(this.context.currentPage) {
                case 'admin':
                    welcomeMsg = "Welcome Admin! 👨‍💼 You can manage events, send notifications, and monitor registrations. Need help with anything?";
                    break;
                case 'events':
                    welcomeMsg = "Looking for events? 🎯 Browse our upcoming workshops and seminars. Want me to help you find something specific?";
                    break;
                case 'notifications':
                    welcomeMsg = "This is your notifications center 🔔 Stay updated with all important announcements!";
                    break;
                case 'profile':
                    welcomeMsg = "Viewing your profile 👤 Check your registered events and account details.";
                    break;
                case 'login':
                    welcomeMsg = "Welcome back! 🔐 Login to access events and manage your registrations.";
                    break;
                case 'register':
                    welcomeMsg = "New here? 📝 Create an account to register for exciting events!";
                    break;
            }
            
            this.addMessage(welcomeMsg, 'bot');
        }, 500);
    }
    
    addMessage(text, sender) {
        const messagesContainer = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        messageDiv.innerHTML = `<div class="message-content">${this.formatMessage(text)}</div>`;
        messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
        
        // Store message
        this.messages.push({ text, sender, timestamp: new Date() });
    }
    
    formatMessage(text) {
        // Convert links
        let formatted = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" style="color: #FFD700;">$1</a>');
        // Convert line breaks
        formatted = formatted.replace(/\n/g, '<br>');
        return formatted;
    }
    
    scrollToBottom() {
        const messagesContainer = document.getElementById('chatbot-messages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    showTyping() {
        const typing = document.getElementById('chatbot-typing');
        typing.style.display = 'flex';
        this.scrollToBottom();
    }
    
    hideTyping() {
        const typing = document.getElementById('chatbot-typing');
        typing.style.display = 'none';
    }
    
    async sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (!message) return;
        
        // Add user message
        this.addMessage(message, 'user');
        input.value = '';
        
        // Show typing indicator
        this.showTyping();
        
        // Process and respond
        setTimeout(() => {
            this.hideTyping();
            const response = this.getAIResponse(message.toLowerCase());
            this.addMessage(response, 'bot');
        }, 500 + Math.random() * 500);
    }
    
    getAIResponse(message) {
        // Intent recognition
        const intents = {
            greetings: ['hello', 'hi', 'hey', 'bonjour', 'salut', 'good morning', 'good afternoon'],
            events: ['events', 'event', 'activities', 'workshop', 'seminar', 'what\'s happening', 'upcoming'],
            register: ['register', 'sign up', 'join', 'participate', 'how to register', 'registration'],
            login: ['login', 'sign in', 'log in', 'account access'],
            admin: ['admin', 'administrator', 'manage', 'dashboard', 'create event', 'delete event'],
            notifications: ['notification', 'notify', 'alert', 'bell', 'message'],
            profile: ['profile', 'my account', 'my info', 'update profile'],
            capacity: ['capacity', 'spots', 'seats', 'full', 'available'],
            certification: ['certificate', 'certification', 'attestation'],
            password: ['password', 'forgot password', 'reset password', 'change password'],
            contact: ['contact', 'email', 'phone', 'address', 'location'],
            time: ['time', 'when', 'schedule', 'date', 'hours'],
            place: ['where', 'location', 'place', 'amphitheater', 'lab', 'hall'],
            help: ['help', 'support', 'assist', 'what can you do', 'features']
        };
        
        // Determine intent
        let detectedIntent = null;
        for (const [intent, keywords] of Object.entries(intents)) {
            if (keywords.some(keyword => message.includes(keyword))) {
                detectedIntent = intent;
                break;
            }
        }
        
        // Generate response based on intent and context
        const responses = {
            greetings: [
                "Hello! 👋 How can I assist you today?",
                "Hi there! 😊 Welcome to ISET Jendouba Events Portal. Need help?",
                "Hey! 👋 I'm here to help you with events, registrations, and more!"
            ],
            events: this.getEventsResponse(message),
            register: this.getRegisterResponse(),
            login: this.getLoginResponse(),
            admin: this.getAdminResponse(),
            notifications: this.getNotificationsResponse(),
            profile: this.getProfileResponse(),
            capacity: "Event capacity depends on the venue. Most events have 40-200 spots. Check the event card for exact numbers! 🎯",
            certification: "Certified events provide official participation certificates. Look for the 'Certification' badge on events! 📜",
            password: "You can reset your password by clicking 'Forgot Password' on the login page. Contact admin if you need help! 🔑",
            contact: "📞 Contact us at: contact@isetj.rnu.tn or call +216 78 123 456. Visit us at Avenue de l'UMA, Jendouba.",
            time: "Event dates and times are shown on each event card. All times are in local Tunisian time (GMT+1). ⏰",
            place: "Event locations vary - check the event card for specific amphitheater, lab, or hall numbers. 📍",
            help: this.getHelpResponse()
        };
        
        if (detectedIntent && responses[detectedIntent]) {
            let response = responses[detectedIntent];
            if (Array.isArray(response)) {
                response = response[Math.floor(Math.random() * response.length)];
            }
            return response;
        }
        
        // Default response
        return "I'm here to help! You can ask me about:\n• 📅 Upcoming events\n• 📝 Registration process\n• 🔔 Notifications\n• 👤 Profile management\n• 📜 Certifications\n\nWhat would you like to know?";
    }
    
    getEventsResponse(message) {
        const currentPage = this.context.currentPage;
        if (currentPage === 'events') {
            return "You're already on the Events page! 🎯 Browse all available events above. Want me to help you find a specific type of event?";
        }
        return "You can view all events by going to the Events page 📅. Click the 'Events' link in the navigation bar above. Would you like me to take you there?";
    }
    
    getRegisterResponse() {
        if (!this.isLoggedIn()) {
            return "To register for events, you need to login first 🔐. Click the 'Login' button in the top menu. Don't have an account? Click 'Register' to create one!";
        }
        return "To register for an event 📝:\n1. Go to Events page\n2. Find an event with available spots\n3. Click 'Register Now'\n\nNeed help finding events?";
    }
    
    getLoginResponse() {
        if (this.isLoggedIn()) {
            return "You're already logged in! 🎉 Check your dashboard or browse events.";
        }
        return "🔐 To login:\n• Admin: admin@isetj.rnu.tn / admin123\n• Students: Register first, then login with your credentials\n\nClick the 'Login' button in the top menu!";
    }
    
    getAdminResponse() {
        if (this.getUserRole() === 'admin') {
            return "👨‍💼 Admin features:\n• Create/Edit/Delete events\n• Send notifications to participants\n• Monitor registrations\n• Manage event capacity\n\nNeed help with a specific admin task?";
        }
        return "Admin access is restricted to authorized personnel only. If you're an admin, please login with admin credentials. 🔒";
    }
    
    getNotificationsResponse() {
        if (!this.isLoggedIn()) {
            return "You need to login to view notifications 🔔. Login first to see your personalized alerts and updates!";
        }
        if (this.context.currentPage === 'notifications') {
            return "You're viewing your notifications! 🔔 Mark them as read by clicking 'Mark as Read'. New notifications appear here automatically.";
        }
        return "🔔 To view notifications:\n• Click the 'Notifications' link in the menu\n• Check for event updates, registration confirmations, and announcements\n\nWant to go there now?";
    }
    
    getProfileResponse() {
        if (!this.isLoggedIn()) {
            return "Please login first to access your profile 👤. Login using the button in the top menu!";
        }
        return "👤 Your profile shows:\n• Your registered events\n• Account information\n• Registration history\n\nClick 'Profile' in the menu to view your details.";
    }
    
    getHelpResponse() {
        return `🤖 I can help you with:
        
📅 Events - Browse and register for workshops/seminars
📝 Registration - Step-by-step registration guide
🔔 Notifications - Check your alerts and updates
👤 Profile - Manage your account
📜 Certifications - Information about certificates
❓ FAQ - Common questions answered

Just type your question or use the quick buttons above! What would you like to know?`;
    }
    
    handleQuickAction(action) {
        const actions = {
            events: () => {
                if (this.context.currentPage !== 'events') {
                    window.location.href = 'events.php';
                } else {
                    this.addMessage("You're already on the Events page! 📅 Scroll up to see all available events.", 'bot');
                }
            },
            register: () => {
                if (!this.isLoggedIn()) {
                    if (this.context.currentPage !== 'login') {
                        this.addMessage("Let me take you to the login page first 🔐", 'bot');
                        setTimeout(() => window.location.href = 'login.php', 1000);
                    }
                } else if (this.context.currentPage !== 'events') {
                    this.addMessage("Taking you to the Events page to register 📝", 'bot');
                    setTimeout(() => window.location.href = 'events.php', 1000);
                } else {
                    this.addMessage("Scroll up to find an event with available spots and click 'Register Now'! 📝", 'bot');
                }
            },
            notifications: () => {
                if (!this.isLoggedIn()) {
                    this.addMessage("Please login first to view your notifications 🔔", 'bot');
                    setTimeout(() => window.location.href = 'login.php', 1500);
                } else if (this.context.currentPage !== 'notifications') {
                    this.addMessage("Opening notifications for you 🔔", 'bot');
                    setTimeout(() => window.location.href = 'notifications.php', 1000);
                } else {
                    this.addMessage("You're already on the Notifications page! 🔔 Check your messages above.", 'bot');
                }
            },
            help: () => {
                this.addMessage(this.getHelpResponse(), 'bot');
            }
        };
        
        if (actions[action]) {
            actions[action]();
        }
    }
    
    isLoggedIn() {
        // Check if user is logged in via session (this will be set in PHP)
        // We can check for elements that only appear when logged in
        const navLinks = document.querySelector('.nav-links');
        if (navLinks && navLinks.innerHTML.includes('Logout')) {
            return true;
        }
        return false;
    }
    
    getUserRole() {
        const navLinks = document.querySelector('.nav-links');
        if (navLinks && navLinks.innerHTML.includes('Admin Panel')) {
            return 'admin';
        }
        return 'student';
    }
    
    loadMessages() {
        // Load previous messages from localStorage
        const saved = localStorage.getItem('iset_chat_messages');
        if (saved) {
            const messages = JSON.parse(saved);
            messages.forEach(msg => {
                this.addMessage(msg.text, msg.sender);
            });
        }
    }
    
    saveMessages() {
        // Save last 50 messages
        const toSave = this.messages.slice(-50);
        localStorage.setItem('iset_chat_messages', JSON.stringify(toSave));
    }
}

// Initialize chatbot when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Make sure Font Awesome is loaded
    if (typeof window.chatbot === 'undefined') {
        window.chatbot = new ISETChatbot();
    }
});