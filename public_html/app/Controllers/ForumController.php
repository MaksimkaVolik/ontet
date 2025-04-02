<?php
namespace App\Controllers;

use App\Models\Forum\Category;
use App\Models\Forum\Thread;
use App\Models\Forum\Post;
use Core\Controller;
use Core\View;

class ForumController extends Controller {
    public function index() {
        $categoryModel = new Category();
        $categories = $categoryModel->getAllWithThreadsCount();
        
        View::render('forum/index.php', [
            'title' => 'Форум',
            'categories' => $categories
        ]);
    }

    public function category($slug) {
        $categoryModel = new Category();
        $category = $categoryModel->findBySlug($slug);
        
        if (!$category) {
            return $this->notFound();
        }
        
        $threads = $categoryModel->getThreads($category['id'], $this->getPage());
        
        View::render('forum/category.php', [
            'title' => $category['title'],
            'category' => $category,
            'threads' => $threads
        ]);
    }

    public function thread($categorySlug, $threadSlug) {
        $threadModel = new Thread();
        $thread = $threadModel->findBySlug($threadSlug);
        
        if (!$thread || $thread['category_slug'] !== $categorySlug) {
            return $this->notFound();
        }
        
        $postModel = new Post();
        $posts = $postModel->getByThread($thread['id'], $this->getPage());
        
        View::render('forum/thread.php', [
            'title' => $thread['title'],
            'thread' => $thread,
            'posts' => $posts
        ]);
    }
    
    protected function getPage() {
        return isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    }
}