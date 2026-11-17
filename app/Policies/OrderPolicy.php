<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Only the order's owner can view it.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    /**
     * Admins can view any order.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }
}