@if(config('card-pkg.DEV'))
    <?php $card_pkg_prefix = '/packages/abs/card-pkg/src';?>
@else
    <?php $card_pkg_prefix = '';?>
@endif

<script type="text/javascript">
    var card_type_list_template_url = "{{asset($card_pkg_prefix.'/public/themes/'.$theme.'/card-pkg/card-type/list.html')}}";
    var card_type_form_template_url = "{{asset($card_pkg_prefix.'/public/themes/'.$theme.'/card-pkg/card-type/form.html')}}";
</script>
<script type="text/javascript" src="{{asset($card_pkg_prefix.'/public/themes/'.$theme.'/card-pkg/card-type/controller.js')}}"></script>
