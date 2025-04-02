document.addEventListener('alpine:init', () => {
    Alpine.data('subscription', (targetType, targetId) => ({
        isSubscribed: false,
        loading: false,
        notificationPrefs: { email: true, push: true },

        init() {
            this.checkSubscription();
        },

        checkSubscription() {
            fetch(`/api/subscriptions/check?target_type=${targetType}&target_id=${targetId}`)
                .then(res => res.json())
                .then(data => {
                    this.isSubscribed = data.subscribed;
                    if (data.prefs) {
                        this.notificationPrefs = data.prefs;
                    }
                });
        },

        toggleSubscription() {
            this.loading = true;
            const formData = new FormData();
            formData.append('target_type', targetType);
            formData.append('target_id', targetId);
            formData.append('notification_prefs', JSON.stringify(this.notificationPrefs));

            fetch(`/api/subscriptions/${this.isSubscribed ? 'unsubscribe' : 'subscribe'}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': Alpine.store('csrfToken') }
            })
            .then(() => {
                this.isSubscribed = !this.isSubscribed;
            })
            .finally(() => this.loading = false);
        }
    });
});