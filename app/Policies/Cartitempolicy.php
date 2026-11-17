<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\User;

class CartItemPolicy
{
    /**
     * Only the cart item's owner can modify or remove it.
     */
    public function modify(User $user, CartItem $cartItem): bool
    {
        return $user->id === $cartItem->user_id;
    }
}