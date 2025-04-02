<?php
namespace App\Controllers;

use App\Models\PartnerOffer;
use Core\Controller;
use Core\View;

class PartnerController extends Controller {
    public function index() {
        $offerModel = new PartnerOffer();
        $offers = $offerModel->getActiveOffers();
        
        View::render('partner/index.php', [
            'title' => 'Партнерская программа',
            'offers' => $offers
        ]);
    }

    public function createOffer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $offerModel = new PartnerOffer();
            $offerId = $offerModel->create([
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'url' => $_POST['url'],
                'image' => $this->handleImageUpload($_FILES['image'])
            ]);
            
            $this->redirect('/partner?created=' . $offerId);
        }
        
        View::render('partner/create.php', ['title' => 'Добавить оффер']);
    }

    private function handleImageUpload($file) {
        $uploader = new \Core\Uploader();
        return $uploader->handlePartnerImageUpload($file);
    }
}