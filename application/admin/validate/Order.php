<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10
 * Time: 8:41
 */

namespace app\admin\validate;


use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'order_no|订单编号' => 'require|alphaNum|unique:order_head',
        'goods_value|商品价格' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'freight|运杂费' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'discount|非现金抵扣金额' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'tax_total|代扣税款' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'actural_paid|实际支付金额' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'currency|币制' => 'require|number|max:3',
        'buyer_name|订购人姓名' => 'require|chsAlpha',
        'buyer_telephone|订购人电话' => 'require|number|max:20',
        'buyer_id_type|订购人证件类型' => 'require|in:1,2',
        'buyer_id_number|订购人证件号码' => 'require|alphaNum|max:20',
        'consignee|收货人姓名' => 'require|chsAlpha',
        'consignee_telephone|收货人电话' => 'require|number|max:20',
        'consignee_address|收货地址' => 'require',
        'logistics_no|物流运单编号' => 'require|alphaNum|unique:order_head',
        'trade_mode|贸易方式' => 'require|number|in:1210,9610',
        'a_country|起运国' => 'require|number|max:3',
        'freight2|运费' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'insured_fee|保费' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'wrap_type|包装种类' => 'require|number|length:1',
        'gross_weight|毛重(公斤)' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'net_weight|净重(公斤)' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'bill_no|提单号' => 'require|alphaNum',
        'voyage_no|航班航次号' => 'require|alphaNum',
        'pack_no|件数' => 'require|number|/^[0-9]{1,19}$/',
        'declare_status|报关状态' => 'in:0,1',
        'pay_status|支付状态' => 'in:0,1',
        'gnum|商品序号' => 'require|number|max:4',
        'item_name|企业商品名称' => 'require',
        'qty|数量' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'qty1|法定数量' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'unit|计量单位' => 'require|number|max:3',
        'unit1|法定计量单位' => 'require|number|max:3',
        'country|原产国' => 'require|number|max:3',
        'hscode|海关商品编码' => 'require|number|max:20',
//        'gjcode|国检商品编号' => 'require|number|max:20',
        'gtype|商品规格型号' => 'require|max:100',
        'price|单价' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'total_price|总价' => 'require|number|/^[0-9]{1,19}(.[0-9]{1,2})?$/',
        'item_currency|商品币制' => 'require|number|max:3'
    ];

    protected $message = [
        'goods_value./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '商品价格小数部分不能超过两位',
        'freight./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '运杂费小数部分不能超过两位',
        'discount./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '非现金抵扣金额小数部分不能超过两位',
        'tax_total./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '代扣税款小数部分不能超过两位',
        'actural_paid./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '实际支付金额小数部分不能超过两位',
        'qty./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '数量小数部分不能超过两位',
        'qty1./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '法定数量小数部分不能超过两位',
        'price./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '单价小数部分不能超过两位',
        'total_price./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '总价小数部分不能超过两位',
        'freight2./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '运费小数部分不能超过两位',
        'insured_fee./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '保费小数部分不能超过两位',
        'gross_weight./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '毛重(公斤)小数部分不能超过两位',
        'net_weight./^[0-9]{1,19}(.[0-9]{1,2})?$/' =>  '净重(公斤)小数部分不能超过两位',
        'pack_no./^[0-9]{1,19}$/' =>  '件数只能是整数',
    ];

    protected $scene = [
        'edit_head' => ['goods_value','freight','discount','tax_total','actural_paid','currency','buyer_name','buyer_telephone','buyer_id_type','buyer_id_number','consignee','consignee_telephone','consignee_address','declare_status','pay_status','trade_mode','a_country','freight2','insured_fee','wrap_type','gross_weight','net_weight','bill_no','voyage_no','pack_no'],
        'edit_goods' => ['item_name','qty','qty1','unit','unit1','country','hscode','gtype','price','total_price','currency'],
        'preview' => ['goods_value','freight','discount','tax_total','actural_paid','currency','buyer_name','buyer_telephone','buyer_id_type','buyer_id_number','consignee','consignee_telephone','consignee_address','declare_status','pay_status','logistics_no','trade_mode','a_country','freight2','insured_fee','wrap_type','gross_weight','net_weight','bill_no','voyage_no','pack_no','item_name','qty','qty1','unit','unit1','country','hscode','gtype','price','total_price','item_currency']
    ];
}