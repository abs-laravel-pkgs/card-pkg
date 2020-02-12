@if(config('card-pkg.DEV'))
    <?php $card_pkg_prefix = '/packages/abs/card-pkg/src';?>
@else
    <?php $card_pkg_prefix = '';?>
@endif

<script type="text/javascript">
    var card_type_list_template_url = "{{asset($card_pkg_prefix.'/public/themes/'.$theme.'/card-pkg/card-type/card_types.html')}}";
</script>
<script type="text/javascript" src="{{asset($card_pkg_prefix.'/public/themes/'.$theme.'/card-pkg/card-type/controller.js')}}"></script>
