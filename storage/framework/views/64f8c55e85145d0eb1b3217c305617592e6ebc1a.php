<!-- litre_logic BEGINS -->
<script>
// Implement Litre Onclick btn
function process_custom_litre(pump_no) {
    let litre = $(`#litre_amount_input_${pump_no}`).val()
    litre = parseFloat(litre);

    let price_litre = $(`#fuel-product-price-${pump_no}`).text();
    price_litre = parseFloat(price_litre);

    let t_price = parseFloat(litre * price_litre).toFixed(2);
    $(`#myr_amount_input_${pump_no}`).val(t_price);
    t_price = t_price + '';
    t_price = t_price.replace('.', '');
    $(`#myr_amount_input_${pump_no}_buffer`).val(t_price);
}
</script>
<!-- litre_logic ENDS -->
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/landing/litre_logic.blade.php ENDPATH**/ ?>