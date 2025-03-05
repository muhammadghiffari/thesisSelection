<x-guest-layout>
    <div class="pt-6 bg-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">Sistem Pemilihan Judul Skripsi</h1>
                        <p class="text-gray-600">Fakultas Ilmu Komputer Universitas Indonesia</p>
                    </div>

                    <!-- Wizard Steps -->
                    <div class="flex justify-center mb-8">
                        <ol class="flex items-center w-full max-w-3xl">
                            <li class="flex w-full items-center text-blue-600 after:content-[''] after:w-full after:h-1 after:border-b after:border-blue-100 after:border-4 after:inline-block">
                                <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full lg:h-12 lg:w-12 shrink-0" :class="{ 'bg-blue-600 text-white': step >= 1, 'bg-blue-100': step < 1 }">
                                    <svg class="w-4 h-4 text-blue-600 lg:w-5 lg:h-5" :class="{ 'text-white': step >= 1, 'text-blue-600': step < 1 }" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                                        <path d="M18 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2ZM6.5 3a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3.5 11.25a.75.75 0 0 1 .75-.75H4.9c.842 0 1.647.335 2.242.931l.83.083a3.993 3.993 0 0 0 2.241.93h2.083a3.993 3.993 0 0 0 2.241-.93l.083-.083a3.142 3.142 0 0 1 2.242-.931h.645a.75.75 0 0 1 0 1.5H14.66a1.64 1.64 0 0 0-1.164.482l-.083.084a5.494 5.494 0 0 1-3.085 1.274h-2.083a5.494 5.494 0 0 1-3.085-1.274l-.083-.084A1.641 1.641 0 0 0 4.9 12h-.645a.75.75 0 0 1-.75-.75Z"/>
                                    </svg>
                                </div>
                            </li>
                            <li class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-100 after:border-4 after:inline-block">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 shrink-0" :class="{ 'bg-blue-600 text-white': step >= 2, 'bg-gray-100': step < 2 }">
                                    <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5" :class="{ 'text-white': step >= 2, 'text-gray-500': step < 2 }" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                                        <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.96 2.96 0 0 0 .13 5H5Z"/>
                                        <path d="M14.066 0H7v5a2 2 0 0 1-2 2H0v11a1.97 1.97 0 0 0 1.934 2h12.132A1.97 1.97 0 0 0 16 18V2a1.97 1.97 0 0 0-1.934-2ZM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm3 5H7a1 1 0 0 1 0-2h6a1 1 0 0 1 0 2Zm0 3H7a1 1 0 0 1 0-2h6a1 1 0 0 1 0 2Z"/>
                                    </svg>
                                </div>
                            </li>
                            <li class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full lg:h-12 lg:w-12 shrink-0" :class="{ 'bg-blue-600 text-white': step >= 3, 'bg-gray-100': step < 3 }">
                                    <svg class="w-4 h-4 text-gray-500 lg:w-5 lg:h-5" :class="{ 'text-white': step >= 3, 'text-gray-500': step < 3 }" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                        <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
                                    </svg>
                                </div>
                            </li>
                        </ol>
                    </div>

                    <!-- Livewire Component -->
                    <div>
                        <livewire:student.thesis-selection-form />
                    </div>

                    <!-- Admin Contact -->
                    <div class="mt-8 text-center text-sm text-gray-600">
                        <p>Mengalami kendala? Hubungi admin melalui:</p>
                        <div class="flex justify-center mt-2 space-x-4">
                            <a href="mailto:admin@cs.ui.ac.id" class="text-blue-600 hover:text-blue-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                admin@cs.ui.ac.id
                            </a>
                            <button class="text-blue-600 hover:text-blue-800 open-chat-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Live Chat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Chat Panel (Hidden by default) -->
    <div id="chat-panel" class="fixed bottom-0 right-0 w-80 bg-white shadow-lg rounded-t-lg overflow-hidden transition-all duration-300 transform translate-y-full">
        <div class="bg-blue-600 text-white p-3 cursor-pointer flex justify-between items-center chat-header">
            <h3 class="font-medium">Live Chat with Admin</h3>
            <button class="text-white close-chat-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="h-72 overflow-y-auto p-3 bg-gray-50 chat-messages">
            <div class="flex flex-col space-y-2">
                <div class="bg-blue-100 p-2 rounded-lg self-start max-w-xs">
                    <p class="text-sm">Halo, ada yang bisa kami bantu?</p>
                    <span class="text-xs text-gray-500">Admin, 10:30</span>
                </div>
                <!-- Chat messages will be appended here -->
            </div>
        </div>
        <div class="p-3 border-t">
            <form id="chat-form" class="flex">
                <input type="text" class="flex-1 border rounded-l-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ketik pesan...">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatPanel = document.getElementById('chat-panel');
            const openChatBtn = document.querySelector('.open-chat-btn');
            const closeChatBtn = document.querySelector('.close-chat-btn');
            const chatHeader = document.querySelector('.chat-header');

            openChatBtn.addEventListener('click', function() {
                chatPanel.classList.remove('translate-y-full');
            });

            closeChatBtn.addEventListener('click', function() {
                chatPanel.classList.add('translate-y-full');
            });

            // Make chat draggable by header
            let isDragging = false;
            let offsetX, offsetY;

            chatHeader.addEventListener('mousedown', function(e) {
                isDragging = true;
                offsetX = e.clientX - chatPanel.getBoundingClientRect().left;
                offsetY = e.clientY - chatPanel.getBoundingClientRect().top;
            });

            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;

                const x = e.clientX - offsetX;
                const y = e.clientY - offsetY;

                chatPanel.style.right = 'auto';
                chatPanel.style.bottom = 'auto';
                chatPanel.style.left = `${x}px`;
                chatPanel.style.top = `${y}px`;
            });

            document.addEventListener('mouseup', function() {
                isDragging = false;
            });

            // Handle chat form submission
            const chatForm = document.getElementById('chat-form');
            const chatMessages = document.querySelector('.chat-messages .flex');

            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const input = this.querySelector('input');
                if (!input.value.trim()) return;

                const now = new Date();
                const time = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();

                // Append user message
                const userMsg = document.createElement('div');
                userMsg.className = 'bg-blue-600 text-white p-2 rounded-lg self-end max-w-xs';
                userMsg.innerHTML = `
                    <p class="text-sm">${input.value}</p>
                    <span class="text-xs text-blue-100">You, ${time}</span>
                `;
                chatMessages.appendChild(userMsg);

                // Clear input
                input.value = '';

                // Scroll to bottom
                chatMessages.parentElement.scrollTop = chatMessages.parentElement.scrollHeight;

                // Here you would typically send the message to your Pusher/WebSocket
                // and then handle the response from the admin
            });
        });
    </script>
</x-guest-layout>
