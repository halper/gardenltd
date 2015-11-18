<!-- jQuery 2.0.2 -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<!-- jQuery UI 1.10.3 -->
<script src="<?= URL::to('/'); ?>/js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
<!-- Bootstrap -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js" type="text/javascript"></script>

<!-- iCheck -->
<!--<script src="<?= URL::to('/'); ?>/vendor/iCheck/icheck.min.js" type="text/javascript"></script>--}}-->

<!-- AdminLTE App -->
<script src="<?= URL::to('/'); ?>/js/manager.js" type="text/javascript"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    $('div.alert-success').not('.alert-important').delay(5000).slideUp(300);

</script>






