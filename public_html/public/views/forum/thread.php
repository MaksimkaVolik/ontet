<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "DiscussionForumPosting",
  "headline": "<?= htmlspecialchars($thread['title']) ?>",
  "description": "<?= htmlspecialchars(substr(strip_tags($thread['content']), 0, 200)) ?>",
  "datePublished": "<?= date('c', strtotime($thread['created_at'])) ?>",
  "author": {
    "@type": "Person",
    "name": "<?= htmlspecialchars($thread['username']) ?>"
  },
  "interactionStatistic": {
    "@type": "InteractionCounter",
    "interactionType": "https://schema.org/ViewAction",
    "userInteractionCount": "<?= $thread['views_count'] ?>"
  }
}
</script>