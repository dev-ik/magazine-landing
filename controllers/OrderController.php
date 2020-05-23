<?php


namespace controllers;

use helpers\HttpHelper;
use models\OrderModel;
use models\OrderProductModel;
use models\UserModel;

/**
 * Class OrderController
 * @package controllers
 */
class OrderController extends Controller
{
    /**
     * @return false|string
     * @throws \Exception
     */
    public function actionCreate()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $userId = (new UserModel())->id;
            $userSession = $this->session->get('user' . $userId);
            $sessionProducts = $userSession['products'] ?? [];
            $products = $this->request->getPost('products');
            $diff = count($products) > count($sessionProducts)
                ? array_diff($products, $sessionProducts)
                : array_diff($sessionProducts, $products);

            if (!empty($diff)) {
                return json_encode([
                   'error' => true,
                   'message' => 'У Вас изменился состав корзины. Обновить корзину?'
                ]);
            }

            $this->getDb()->startTransaction();

            $order = new OrderModel();
            if ($orderId = $order->save()) {
                foreach ($sessionProducts as $sessionProduct) {
                    $orderProduct = new OrderProductModel();
                    $orderProduct->product_id = (int)$sessionProduct;
                    $orderProduct->order_id = (int)$orderId;
                    if (!$orderProduct->save()){
                        $this->getDb()->rollBackTransaction();
                        break;
                    }
                }
            }

            $this->getDb()->endTransaction();


            if ($orderId) {
                return json_encode([
                    'error' => false,
                    'orderId' => $orderId
                ]);
            }
        }

        throw new \Exception('400 Bad Request', 400);
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function actionPayment()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {

            $orderId = $this->request->getPost('orderId');
            /** @var OrderModel $order */
            $order = OrderModel::find()->where([
                'id' => $orderId,
                'payment' => false
            ])->one();

            if (!$order) {
                return json_encode([
                    'error' => true,
                    'message' => 'Несуществующий или оплаченый заказ'
                ]);
            }

            $response = HttpHelper::sendRequest();
            $httpCode = $response['header']['http_code'] ?? null;

            if ($httpCode === 200) {
                $order->payment = true;

                if ($order->save()) {
                    $userId = (new UserModel())->id;
                    $this->session->set('user' . $userId, []);
                    return json_encode(['error' => false]);
                }
                return json_encode(['error' => true]);
            }
        }

        throw new \Exception('400 Bad Request', 400);

    }

}