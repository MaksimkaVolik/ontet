<?php
namespace App\Controllers\Api;

use App\Models\Reaction;
use Core\Controller;

class ReactionController extends Controller {
    public function addReaction() {
        $this->validateCsrfToken();
        
        $postId = (int)$_POST['post_id'];
        $userId = (int)$_SESSION['user_id'];
        $type = $_POST['type'];

        if (!in_array($type, ['like', 'dislike', 'laugh', 'love', 'surprise', 'idea'])) {
            http_response_code(400);
            exit;
        }

        $reactionModel = new Reaction();
        $result = $reactionModel->add($postId, $userId, $type);

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}