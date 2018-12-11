<?php
$hoster = "https://3c-capi.mobifone.vn/";
?>

<html>
    <head>
        <script src="<?= $hoster ?>/js/embed/jquery.min.js" type="text/javascript"></script>    
        <script src="<?= $hoster ?>/js/embed/jssip-3.2.10.js" type="text/javascript"></script>
        <script src="<?= $hoster ?>/js/embed/init-3.0.7.js" type="text/javascript"></script>
        <script src="<?= $hoster ?>/js/embed/web.push.js" type="text/javascript"></script>
        <script src="<?= $hoster ?>/js/embed/cs_const.js" type="text/javascript"></script>
        <script src="<?= $hoster ?>/js/embed/cs_voice.js" type="text/javascript"></script>

        <script src="/test_website/js/custom.js" type="text/javascript"></script>
        <script>
            csInit("your_registered_token", "your_domain");
        </script>
        <script>
            function transferCall() {
                getTransferAgent();
                csTransferCallAgent("your_aegnt_ipphone");
            }
            function transferCallToAcd() {
                csTransferCallAcd("your_queue_acd_id");
            }

            function onCallout() {
                csCallout($('#phoneNumber').val());
            }
        </script>
    </head>
    <body>
        <video id="my-video" autoplay style="display: none;" src="<?=$hoster?>/images/320x240.ogg">
        </video>
        <video id="peer-video" autoplay style="display: none;" src="<?=$hoster?>/images/320x240.ogg">
        </video>
        
        <button id="enable" onclick="csEnableCall()">1. Kích hoạt thoại </button><br/>
        <button id="enable" onclick="changeCallStatus()">2. On/Off trạng thái</button><br/>
        <input type="text" id="phoneNumber"/><button onclick="onCallout()">3. Gọi ra</button><br/>
        <button onclick="endCall();">4.Kết thúc cuộc gọi ra</button><br/><br/><br/><br/>

        <label id="phoneNo"></label><br/>
        <button onclick="onAcceptCall();">I. Nhận cuộc gọi vào</button><br/>
        <button onclick="muteCall();">II.Mute/Unmute</button><br/>
        <button onclick="holdCall();">III.Hold/Unhold</button><br/>
        <button onclick="endCall();">IV.Kết thúc cuộc gọi vào</button><br/>
        <button onclick="transferCall();" id="transferCall" disabled>V.Chuyển cuộc gọi</button><br/>
        <button onclick="transferCallToAcd();" id="transferCallAcd" disabled>VI.Chuyển cuộc gọi sang nhánh acd</button><br/>
        <button onclick="responseTransferAgent(1);" id="transferResponseOK" disabled>VII.Chấp nhận chuyển cg</button><br/>
        <button onclick="responseTransferAgent(0);" id="transferResponseReject" disabled>VII.Từ chối chuyển cg</button><br/>
    </body>
</html>
