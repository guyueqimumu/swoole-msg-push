<?php
namespace msgPush\Api\Controller;

class User
{
    public function getAllUser()
    {
        $data = [
            'name'=>'里见菜惠子',
            'gender'=>'female',
        ];
        return json_encode($data);
    }
}