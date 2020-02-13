<?php

/**
 * Utilita na výpočet DPH a cien z daňou a bez dane metódami zhora nadol a zdloa nahor (podľa zákona)
 * Výpočet zdola - základom dane je cena bez DPH - daná čiastka sa vynásobí percentuálnou sadzbou.
 * Výpočet zhora - základom dane je cena vrátane DPH - čiastka sa vynásobí koeficientom (napr. 20/120).
 * https://www.stormware.sk/xml/proVyvojare/vypocetCastky.aspx
 *
 */
class Price
{
    public static function formatPercentToDecimal($percent)
    {
        if ($percent > 1) {
            $percent /= 100;
        }

        return $percent;
    }

    public static function formatPercentToWholeNumber($percent)
    {
        if ($percent < 1) {
            $percent *= 100;
        }

        return $percent;
    }

    public static function getVatUpDown($priceWithVat, $vatNumber, $roundPrecision = 2)
    {
        $vatNumber = self::formatPercentToWholeNumber($vatNumber);
        $vat = $priceWithVat * ($vatNumber / (100 + $vatNumber));

        if (!is_numeric($vat) || $vat < 0) {
            throw new \Exception('Incorect vat (not number or negative).');
        }

        return round($vat, $roundPrecision);
    }

    public static function getPriceWithoutVatUpDown($priceWithVat, $vatNumber, $roundPrecision = 2)
    {
        $vatNumber = self::formatPercentToWholeNumber($vatNumber);
        $vat = self::getVatUpDown($priceWithVat, $vatNumber, $roundPrecision);
        $sumWithoutVat = $priceWithVat - $vat;

        if (!is_numeric($vat) || $sumWithoutVat < 0) {
            throw new \Exception('Incorect sum without vat (not number or negative).');
        }

        return round($sumWithoutVat, $roundPrecision);
    }

    /**
     * Vráti hodnotu DPH zo základu DPH
     */
    public static function getVatDownUp($priceWithoutVat, $vatNumber, $roundPrecision = 2)
    {
        $vatNumber = self::formatPercentToWholeNumber($vatNumber);
        $vat = $priceWithoutVat * ($vatNumber / 100);

        return round($vat, $roundPrecision);
    }

    /**
     * Cena s DPH vypočítaná metódou zdola nahor
     * @param  double  $priceWithoutVat
     * @param  $vatNumber percent
     * @param  integer $roundPrecision  počet miest zaokrúhlenia
     * @return double
     */
    public static function getPriceWithVatDownUp($priceWithoutVat, $vatNumber, $roundPrecision = 2)
    {
        $vatNumber = self::formatPercentToWholeNumber($vatNumber);
        return $priceWithoutVat + self::getVatDownUp($priceWithoutVat, $vatNumber, $roundPrecision);
    }

    /**
     *  Vráti hodnotu zľavy v Eurách
     * @param  $price          pôvodá cena
     * @param  $discount      zľava v percentáchach
     * @param  $roundPrecision
     */
    public static function getPriceDiscount($price, $discount, $roundPrecision = 2)
    {
        $discount = self::formatPercentToWholeNumber($discount);
        $priceDiscount = $price * ($discount / 100);

        return round($priceDiscount, $roundPrecision);
    }

    /**
     * Vráti cenu po aplikovaní žľavy
     * @return double cena po aplikovaní zľavy
     */
    public static function getPriceWithDiscount($price, $discount, $roundPrecision = 2)
    {
        $discount = self::formatPercentToWholeNumber($discount);

        $discountPrice = self::getPriceDiscount($price, $discount, $roundPrecision);

        return round($price - $discountPrice, $roundPrecision);
    }
}
