<?php


namespace controllers;

use models\CategoryModel;
use models\ProductModel;
use models\UserModel;

/**
 * Class SiteController
 * @package controllers
 */
class SiteController extends Controller
{
    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex(): string
    {
        $userModel = new UserModel();

        /** @var CategoryModel[] $categories */
        $categories = CategoryModel::find()->where(['active' => 1])->limit(3)->all();

        /** @var CategoryModel $category */
        foreach ($categories as $category) {

            $products[$category['id']] = ProductModel::find()->where([
                'active' => 1,
                'category_id' => $category['id']
            ])->limit(4)->all();
        }

        $userSession = $this->session->get('user' . $userModel->id);
        $userProducts = $userSession['products'] ?? [];

        return $this->view->render('index', [
            'categories' => $categories,
            'products' => $products,
            'userProducts' => $userProducts
        ]);
    }

}