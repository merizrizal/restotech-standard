import { createApp } from 'vue';

const root = document.querySelector('[data-restotech-pos-app]');

if (root) {
    const mountPoint = document.querySelector('#restotech-pos-mount');
    const openTableSessionEndpoint = root.dataset.openTableSessionEndpoint;
    const csrfToken = root.dataset.csrfToken;
    const initialTableId = root.dataset.demoTableId ? Number(root.dataset.demoTableId) : '';

    createApp({
        data() {
            return {
                tableId: Number.isFinite(initialTableId) ? initialTableId : '',
                loading: false,
                statusMessage: Number.isFinite(initialTableId)
                    ? `Demo table ${initialTableId} is preloaded.`
                    : 'Ready to open a table session.',
                errorMessage: '',
                session: null,
            };
        },
        methods: {
            async openTableSession() {
                this.loading = true;
                this.errorMessage = '';
                this.statusMessage = 'Opening table session...';

                try {
                    const response = await fetch(openTableSessionEndpoint, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            dining_table_id: this.tableId,
                        }),
                    });

                    const payload = await response.json();

                    if (! response.ok) {
                        throw new Error(payload.message ?? 'Unable to open table session.');
                    }

                    this.session = payload.data;
                    this.statusMessage = `Opened table session ${payload.data.id}.`;
                } catch (error) {
                    this.errorMessage = error instanceof Error ? error.message : 'Unable to open table session.';
                    this.statusMessage = 'Ready to try again.';
                } finally {
                    this.loading = false;
                }
            },
        },
        template: `
            <div class="grid">
                <label>
                    Dining table ID
                    <input v-model.number="tableId" type="number" min="1" placeholder="1">
                </label>

                <button type="button" @click="openTableSession" :disabled="loading">
                    {{ loading ? 'Opening…' : 'Open table session' }}
                </button>

                <p v-if="statusMessage" class="status">{{ statusMessage }}</p>
                <p v-if="errorMessage" class="error">{{ errorMessage }}</p>

                <pre v-if="session">{{ JSON.stringify(session, null, 2) }}</pre>
            </div>
        `,
    }).mount(mountPoint ?? root);
}
