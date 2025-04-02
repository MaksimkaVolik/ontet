document.addEventListener('alpine:init', () => {
    Alpine.data('reactions', (postId) => ({
        loading: false,
        reactions: [],
        userReaction: null,

        init() {
            this.fetchReactions();
        },

        fetchReactions() {
            fetch(`/api/post/${postId}/reactions`)
                .then(res => res.json())
                .then(data => {
                    this.reactions = data.reactions;
                    this.userReaction = data.user_reaction;
                });
        },

        react(type) {
            if (this.loading) return;
            this.loading = true;

            const formData = new FormData();
            formData.append('post_id', postId);
            formData.append('type', type);
            formData.append('csrf_token', Alpine.store('csrfToken'));

            fetch('/api/reactions/add', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                this.reactions = data.reactions;
                this.userReaction = data.user_reaction;
            })
            .finally(() => this.loading = false);
        }
    }));
});