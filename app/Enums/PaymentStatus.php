<?php


namespace App\Enums;

enum PaymentStatus {
   case Paid;
   
   case Unpaid;

   case PartiallyPaid;

   public static function from(string $state): self
   {
       return match ($state) {
           'paid' => self::Paid,
           'unpaid' => self::Unpaid,
           'partially_paid' => self::PartiallyPaid
       };
   }

   public function label(): string
   {
       return match ($this) {
           self::Paid => 'Paid',
           self::Unpaid => 'Un Paid',
           self::PartiallyPaid => 'Partially Paid',
       };
   }
}