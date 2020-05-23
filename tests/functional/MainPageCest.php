<?php
namespace tests\functional;


class MainPageCest
{
    /**
     * @param \FunctionalTester $i
     * @throws \Exception
     */
    public function testMainPage(\FunctionalTester $i): void
    {
       $i->amOnPage('/');

       $csrf = $i->grabAttributeFrom('meta[name="_csrf"]','content');

       $i->sendAjaxPostRequest('/cart/add',[
           'product' => 5,
           '_csrf' => $csrf,
       ]);

       $result = json_decode($i->grabResponse(),1);

       $i->assertArrayHasKey('error',$result);

       $i->assertFalse($result['error']);
    }
}