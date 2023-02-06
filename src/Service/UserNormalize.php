<?php

namespace App\Service;

use App\Entity\User;

class UserNormalize {

    /**
     * Normalize a user.
     * 
     * @param User $user
     * 
     * @return array|null
     */

    public function UserNormalize(User $user): ?array {

        $data = [
            // 'id' => $user->getId(),
            'name' => $user->getName(),
            'surname1' => $user->getSurname1(),
            'surname2' => $user->getSurname2(),
            'phone_number' => $user->getPhoneNumber(),
            'city' => $user->getCity(),
            'address' => $user->getAddress(),
            'email' => $user->getEmail(),
            'password'=>$user->getPassword()
        ];

        return $data;
    }
};