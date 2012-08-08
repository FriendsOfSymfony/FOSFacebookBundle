<div id="fb-root"></div>
<?php if (empty($async)) { ?>
<script type="text/javascript" src="http://connect.facebook.net/<?php echo $culture ?>/all.js"></script>
<?php } ?>
<script type="text/javascript">
<?php if (!empty($async)) { ?>
window.fbAsyncInit = function() {
<?php }?>
  FB.init(<?php echo json_encode(array(
    'appId'   => $appId,
    'xfbml'   => $xfbml,
    'status' =>  $status,
    'oauth'   => $oauth,
    'cookie'  => $cookie,
    'channelUrl' => $channelUrl,
    'logging' => $logging)) ?>);
<?php if (!empty($async)) { ?>
    <?php echo $fbAsyncInit ?>
  };

(function() {
  var e = document.createElement('script');
  e.src = document.location.protocol + <?php echo json_encode('//connect.facebook.net/'.$culture.'/all.js') ?>;
  e.async = true;
  document.getElementById('fb-root').appendChild(e);
})();
<?php } ?>
</script>
