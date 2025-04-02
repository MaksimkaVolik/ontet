class FeedManager {
    constructor() {
        this.feedContainer = document.getElementById('feed-container');
        this.initEventListeners();
        this.loadFeed('hot');
    }

    initEventListeners() {
        document.querySelectorAll('.feed-sort-btn').forEach(btn => {
            btn.addEventListener('click', () => this.loadFeed(btn.dataset.sort));
        });
    }

    async loadFeed(sortType) {
        const response = await fetch(`/api/feed?sort=${sortType}`);
        const threads = await response.json();
        
        this.feedContainer.innerHTML = threads.map(thread => `
            <div class="thread-card">
                <h3><a href="/thread/${thread.id}">${thread.title}</a></h3>
                <div class="thread-meta">
                    <span>${thread.comments_count} comments</span>
                    <span>${thread.score.toFixed(1)} points</span>
                </div>
            </div>
        `).join('');
    }
}

new FeedManager();