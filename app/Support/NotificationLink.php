<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Trouve la page vers laquelle une notification doit mener (produit, commande,
 * paiement, message…), à partir de son titre et de son texte.
 * Renvoie null si aucune page ne correspond : la notification n'est alors pas cliquable.
 */
class NotificationLink
{
    public static function url($notification, bool $isAdmin = false): ?string
    {
        try {
            $text = trim(($notification->title ?? '') . ' ' . ($notification->content ?? ''));
            $lower = mb_strtolower($text);

            // 1) Produit : « Nom du produit »
            if (str_contains($lower, 'produit') && preg_match('/«\s*(.+?)\s*»/u', $text, $m)
                && class_exists(\App\Models\Product::class)) {
                $product = \App\Models\Product::where('name', $m[1])->first();
                if ($product) {
                    return $isAdmin
                        ? self::route('admin.products.edit', $product)
                        : self::route('products.show', $product);
                }
            }

            // 2) Commande : numéro de type CMD-XXXXXXXX
            if (preg_match('/\bCMD-[A-Z0-9]+\b/', $text, $m)) {
                if ($isAdmin) {
                    return str_contains($lower, 'paiement')
                        ? self::route('admin.payments.index')
                        : self::route('admin.orders.index');
                }

                $order = class_exists(\App\Models\Order::class)
                    ? \App\Models\Order::where('order_number', $m[0])->first()
                    : null;

                if ($order && Route::has('orders.show')) {
                    return route('orders.show', $order);
                }

                return self::route('orders.index');
            }

            // 3) Par thème
            $themes = [
                ['paiement', 'payments.index', 'admin.payments.index'],
                ['livraison', 'deliveries.index', 'admin.deliveries.index'],
                ['commande', 'orders.index', 'admin.orders.index'],
                ['signalement', 'reports.index', 'admin.reports.index'],
                ['message', 'messages.index', 'admin.messages.index'],
                ['avis', 'orders.index', 'admin.reviews.index'],
            ];
            foreach ($themes as [$word, $clientRoute, $adminRoute]) {
                if (str_contains($lower, $word)) {
                    return self::route($isAdmin ? $adminRoute : $clientRoute);
                }
            }
        } catch (\Throwable $e) {
            // jamais d'erreur d'affichage à cause d'un lien
        }

        return null;
    }

    private static function route(string $name, $parameter = null): ?string
    {
        if (! Route::has($name)) {
            return null;
        }

        return $parameter === null ? route($name) : route($name, $parameter);
    }
}
