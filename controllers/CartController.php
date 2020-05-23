<?php

namespace controllers;

use models\OrderModel;
use models\OrderProductModel;
use models\ProductModel;
use models\UserModel;

class CartController extends Controller
{
    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex(): string
    {
        $userId = (new UserModel())->id;
        $userSession = $this->session->get('user' . $userId);
        $sessionProducts = $userSession['products'] ?? [];

        if (!empty($sessionProducts)) {
            /** @var ProductModel[] $cartProducts */
            $cartProducts = ProductModel::find()->where([
                'active' => 1,
                'id' => $sessionProducts
            ])->all();
        }

        return $this->view->render('cart', [
            'cartProducts' => $cartProducts ?? []
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionCheckout(): string
    {
        $get = $this->request->get('get');
        $orderId = (int)($get['order'] ?? 0);

        if (!$orderId) {
            return $this->view->render('cart', ['cartProducts' => []]);
        }

        /** @var OrderModel $order */
        $order = OrderModel::find()->where(['id' => $orderId])->one();

        if (!$order) {
            return $this->view->render('cart', ['cartProducts' => []]);
        }

        /** @var OrderProductModel $order */
        $productsIds = OrderProductModel::find()
            ->select(['product_id'])
            ->where(['order_id' => $orderId])->all();

        $idsProduct = [];

        foreach ($productsIds as $productsId) {
            $idsProduct[] = $productsId['product_id'];
        }

        /** @var ProductModel[] $orderProducts */
        $orderProducts = ProductModel::find()->where([
            'active' => true,
            'id' => $idsProduct
        ])->all();

        if (!$orderProducts) {
            return $this->view->render('cart', ['cartProducts' => []]);
        }

        return $this->view->render('checkout', [
            'orderProducts' => $orderProducts ?? [],
            'paid' => $order->payment, 'orderId' => $orderId
        ]);
    }

    /**
     * @throws \Exception
     */
    public function actionAdd(): string
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $productId = $this->request->getPost('product');

            if ($productId) {
                /** @var ProductModel|null $product */
                $product = ProductModel::find()->where(['id' => $productId])->one();

                if (!$product) {
                    return json_encode(['error' => true]);
                }

                $userId = (new UserModel())->id;
                $userSession = $this->session->get('user' . $userId);
                $userSession['products'] = $userSession['products'] ?? [];
                array_push($userSession['products'], $productId);
                $this->session->set('user' . $userId, $userSession);

                return json_encode(['error' => false]);
            }
        }

        throw new \Exception('400 Bad Request', 400);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionDelete(): string
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $productId = $this->request->getPost('product');
            if ($productId) {
                $userId = (new UserModel())->id;
                $userSession = $this->session->get('user' . $userId);
                $userSession['products'] = $userSession['products'] ?? [];

                if (($key = array_search($productId, $userSession['products'])) !== false) {
                    unset($userSession['products'][$key]);
                }
                array_diff($userSession['products'], [null]);

                $this->session->set('user' . $userId, $userSession);
                return json_encode(['error' => false]);
            }
        }

        throw new \Exception('400 Bad Request', 400);
    }
}