<x-layouts.app :title="'Help'">
<section class="grid gap-8 lg:grid-cols-[1fr,0.9fr]">
    <div class="space-y-6" x-data="helpFaq">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Help</h1>
            <p class="mt-2 text-slate-600">Find quick answers about reporting, tracking, and status updates.</p>
        </div>
        <div class="card space-y-4">
            <h2 class="text-xl font-semibold">FAQ</h2>
            <div class="space-y-3">
                <label class="input-label" for="help-search">Search questions</label>
                <input
                    id="help-search"
                    x-model.trim="query"
                    type="text"
                    placeholder="Search help questions"
                    class="text-input"
                >
            </div>
            <div class="space-y-4">
                <template x-for="(item, index) in filteredFaqs" :key="index">
                    <div>
                        <h3 class="font-semibold" x-text="item.question"></h3>
                        <p class="mt-1 text-slate-600" x-text="item.answer"></p>
                    </div>
                </template>
                <p x-show="filteredFaqs.length === 0" x-cloak class="text-sm text-slate-500">
                    No matching help questions were found. Try a different keyword.
                </p>
            </div>
        </div>
    </div>

    <div class="card" x-data="helpBot">
        <h2 class="text-xl font-semibold">Quick help bot</h2>
        <div class="mt-4 h-96 space-y-3 overflow-y-auto rounded-2xl bg-slate-50 p-4">
            <template x-for="(message, index) in messages" :key="index">
                <div :class="message.role === 'bot' ? 'mr-10 rounded-2xl bg-white p-3 text-sm text-slate-700' : 'ml-10 rounded-2xl bg-civic-600 p-3 text-sm text-white'">
                    <p x-text="message.text"></p>
                </div>
            </template>
            <div x-show="loading" x-cloak class="mr-10 rounded-2xl bg-white p-3 text-sm text-slate-500">
                Thinking...
            </div>
        </div>
        <div class="mt-4 flex gap-3">
            <input x-model="input" @keydown.enter.prevent="send" type="text" placeholder="Ask about reports, statuses, maps, or admin review" class="text-input" :disabled="loading">
            <button @click="send" class="btn-primary" type="button" :disabled="loading">
                <span x-text="loading ? 'Sending...' : 'Send'"></span>
            </button>
        </div>
        <p class="mt-3 text-sm text-slate-500">This chatbot uses your local LM Studio model, so LM Studio must be running on your computer while you use it.</p>
    </div>
</section>
</x-layouts.app>
