# Demo-Embed-3C
**Giải pháp tích hợp luồng voice giữa ứng dụng web bên thứ 3 và 3C**

1. **Mô tả các chức năng**

- Hỗ trợ các loại ứng dụng web (web app) tích hợp tổng đài thoại 3C.

- Người dùng (user) của doanh nghiệp có thể thực hiện việc tiếp nhận cuộc gọi hay gọi điện ra ra cho khách hàng thông qua số hotline 3C của Mobifone.

- Popup thông tin khách hàng khi có cuộc gọi đến

- Công nghệ sử dụng WebRTC không cần cài đặt thêm phần mềm nghe gọi điện.

- Hỗ trợ tích hợp với các thiết bị nghe gọi như IP Phone, Softphone

2. **Khởi tạo**

- Tạo tài khoản (domain), khai báo agent trên 3C
- Lấy access_key của domain từ bộ phận hỗ trợ 3C và lưu lại trên CRM. Phần mềm CRM lưu ý bảo mật access_key này.

3. **Hướng dẫn tích hợp**

**Chú ý:** Toàn bộ mã nguồn Demo của việc tích hợp với hệ thống tổng đài 3C được upload tại đây.

Sau khi tải về, giải nén và sử dụng các server ảo như XAMPP để chạy thử demo.

**Bước 1: Tạo token và đăng nhập vào hệ thống**

- Khi agent đăng nhập trên CRM, CRM cần thực hiện gen 1 chuỗi **Token** và gửi sang 3C để thực hiện đăng nhập trên 3C. **Token** :là chuỗi mã hóa theo chuẩn JWT (xem thêm ở [https://jwt.io/](https://jwt.io/)), bạn sẽ cần lập trình để gen ra đoạn mã hóa này với thuật toán "HS256", secret key là access\_key lấy được ở bước 1 và phần Payload có dạng như bên dưới:

        Payload:

        {
          "ipphone": "5000", // ext của agent
          "expired": "2017-07-30 00:00:00" // thời hạn của token, 3C sẽ validate tham số này, nếu không truyền sang sẽ có   mặc định
        }

- Sau khi gen token, CRM gọi service bên dưới để login vào 3C

POST:   [https://3c-capi.mobifone.vn/{domain}/thirdParty/login](https://3c-capi.mobifone.vn/{domain}/thirdParty/login)

Request

        {
          "token": "AFADFxadfsdf_123adafasdf"
        }

Response

        status: 200

        {
            "code": "ok",
            "user":
            {
              "user_id": 35436,
              "token": "oken",
              ...
            }
        }

Sau khi tải project demo về, sửa lại access key, domain và agent_id trong file login.php cho đúng với tài khoản được cấp và truy cập vào [http://localhost/demoEmbed/login.php](http://localhost/demoEmbed/login.php) để thực hiện đăng nhập và lấy token sẽ dùng trong bước 2.

**Bước 2: Lập trình tích hợp tiếp nhận cuộc gọi 3C vào CRM**

**Chú ý:** Nếu bạn lập trình trên localhost, cài extension "Allow Cross Domain"  trên Chrome để không bị lỗi Cross Domain.

        Truy cập [http://localhost/demoEmbed/](http://localhost/demoEmbed/) để thực hiện test gọi trên project demo.

- Import các lib javascript đã được 3C cung cấp sẵn vào CRM (trong đó custom.js là file js chứa các hàm 3C định nghĩa sẵn để hỗ trợ thông báo, xử lý giao diện, tương tác tuỳ theo nhu cầu của từng CRM)

        <script src="https://3c-capi.mobifone.vn/js/embed/jquery.min.js" type="text/javascript"></script>
        <script src="https://3c-capi.mobifone.vn/js/embed/jssip-3.0.7.js" type="text/javascript"></script>
        <script src="https://3c-capi.mobifone.vn/js/embed/init-3.0.7.js" type="text/javascript"></script>
        <script src="https://3c-capi.mobifone.vn/js/embed/web.push.js" type="text/javascript"></script>
        <script src="https://3c-capi.mobifone.vn/js/embed/cs_const.js" type="text/javascript"></script>
        <script src="https://3c-capi.mobifone.vn/js/embed/cs_voice.js" type="text/javascript"></script>
        <script src="custom.js" type="text/javascript"></script>

- Copy dòng sau giữa thẻ body:

        <video id="my-video" autoplay style="display: none;" src="https://3c-web1.mobifone.vn/images/320x240.ogg"></video>
        <video id="peer-video" autoplay style="display: none;" src="https://3c-web1.mobifone.vn/images/320x240.ogg"></video>

- Thực hiện việc kết nối với tổng đài 3C qua hàm sau:

                csInit(token, domain);

Trong đó token là token đã được tạo ra ở bước 1

- Tại một thời điểm, chỉ 1 tab có quyền gọi và tiếp nhận cuộc gọi. Để cho 1 tab bật quyền tiếp nhận cuộc gọi, gọi hàm **csEnableCall();**
- Hệ thống hỗ trợ 2 loại thiết bị nghe gọi:
  - Nghe gọi trên trình duyệt
  - Nghe gọi qua softphone, IP Phone

Tuỳ thuộc vào loại thiết bị sẽ sử dụng mà chọn thiết bị bằng cách gọi hàm sau:

                changeDevice(type);

 Trong đó: 
 - type = 1: nghe gọi trên trình duyệt
 - type = 2: nghe gọi qua softphone, IP Phone

Trong trường hợp muốn tắt trình duyệt đi mà vẫn tiếp nhận được cuộc gọi qua GSM, chuyển type = 3 và truyền vào số điện thoại di động sẽ tiếp nhận cuộc gọi.

                changeDevice(3, '093xxxxxxxx');

Sau khi F5, hoặc đăng xuất, không sử dụng nữa thì phải huỷ đăng ký gọi và chuyển về thiết bị gọi mặc định để có thể tiếp tục sử dụng bình thường bằng cách sử dụng đoạn code sau:

                $(window).bind('unload', function () {
                        csUnregister();
                        if (csVoice.enableVoice) {
                                reConfigDeviceType();
                        }
                });

- Sau khi kết nối thành công với tổng đài, ngay lúc này đã có thể nhận và gọi điện tới khách hàng. Các hàm dùng để tương tác với hệ thống:

| STT | Hàm | Mô tả |
| --- | --- | --- |
| 1 | csEnableCall(); | 3C chỉ cho phép tại 1 thời điểm có 1 tab được quyền gọi. Vì thế nếu bật nhiều tab hoặc đăng nhập tại nhiều nơi thì cũng chỉ có duy nhất 1 tab được sử dụng chức năng thoại. Tab đầu tiên đăng nhập vào hệ thống sẽ có quyền thoại này. Các tab về sau muốn dùng thì sử dụng hàm này |
| 2 | changeCallStatus(); | 3C có hỗ trợ trạng thái tiếp nhận cuộc gọi là On/Off. Nếu để trạng thái là On thì cuộc gọi sẽ đổ về. Ngược lại thì sẽ không đổ về. Để chuyển qua lại 2 trạng thái thì sẽ dùng hàm này |
| 3 | csCallout(number); | Gọi điện thoại tới số điện thoại của khách hàng (number là số điện thoại cần gọi) |
| 4 | onAcceptCall(); | Tiếp nhận cuộc gọi đến |
| 5 | endCall(); | Kết thúc cuộc gọi |
| 6 | holdCall(); | Hold/Unhold cuộc gọi |
| 7 | muteCall(); | Mute/Unmute cuộc gọi. Sự kiện này được gọi khi cuộc gọi bị Mute. Hàm này chỉ hoạt động cho cuộc gọi nhận từ khách hàng, không hoạt động cho cuộc gọi ra |
| 8 | changeDevice(type) | Thay đổi thiết bị tiếp nhận cuộc gọi |
| 9 | reConfigDeviceType() | Đối với nhân viên có vai trò Extension, Mobile hay 3C Softphone, khi đăng nhập vào để gọi điện sẽ gây bất đồng bộ khiến không thể tiếp nhận cuộc gọi trên các thiết bị mặc định được nữa. Gọi hàm này khi đăng xuất hoặc F5 web tích hợp để chuyển thiết bị nhân viên này về mặc định và tiếp nhận cuộc gọi như bình thường |
| 10 | getTransferAgent() | Lấy thông tin agent đang online |
| 11 | csTransferCallAgent(ipphone) | Chuyển cuộc gọi qua agent khác, `ipphone` số ipphone của agent muốn chuyển,Chú ý: agent phải đang trong cuộc gọi mới có thể chuyển cuộc gọi |
| 12 | csTransferCallAcd(queueId) | Chuyển cuộc gọi qua nhánh acd khác, `queueId` id của nhánh acd muốn chuyển,Chú ý: agent phải đang trong cuộc gọi mới có thể chuyển cuộc gọi |
| 13 | responseTransferAgent(action) | Tiếp nhận cuộc gọi được chuyển,`action=1` đồng ý,`action=0` từ chối |

- Ngoài ra, khi muốn thay đổi giao diện hoặc xử lý thông tin mỗi khi có một sự kiện của cuộc gọi, 3C hỗ trợ các hàm sau

**Chú ý: Tất cả các hàm dưới đây đều phải được implement để đảm bảo tính đúng đắn của chương trình**

| STT | Hàm | Bắt buộc | Mô tả |
| --- | --- | --- | --- |
| 1 | csCallRinging(phone) |   | Sự kiện này được gọi khi có cuộc gọi đến nhân viên (phone là số điện thoại gọi đến) |
| 2 | csAcceptCall (); |   | Sự kiện này được gọi khi nhân viên tiếp nhận cuộc gọi |
| 3 | csEndCall(); |   | Sự kiện này được gọi khi cuộc gọi kết thúc. |
| 4 | csMuteCall(); |   | Sự kiện này được gọi khi cuộc gọi bị Mute. Hàm này chỉ hoạt động cho cuộc gọi nhận từ khách hàng, không hoạt động cho cuộc gọi ra |
| 5 | csUnMuteCall(); |   | Sự kiện này được gọi khi cuộc gọi bị unMute. Hàm này chỉ hoạt động cho cuộc gọi nhận từ khách hàng, không hoạt động cho cuộc gọi ra |
| 6 | csHoldCall(); |   | Sự kiện này được gọi khi cuộc gọi bị Hold |
| 7 | csUnHoldCall(); |   | Sự kiện này được gọi khi cuộc gọi bị UnHold |
| 8 | showCalloutInfo(number) |   | Sự kiện này được gọi khi có nhân viên gọi ra ngoài cho khách hàng (number là số gọi đi) |
| 9 | showCalloutError(errorCode, sipCode) |   | Sự kiện này được gọi khi có xảy ra lỗi lúc gọi đến khách hàng (errorCode, sipCode sẽ được mô tả trong bảng dưới) |
| 10 | csShowEnableVoice(isEnable) |   | Sự kiện này xảy ra khi một tab này được kích hoạt hay bị tắt chức năng thoại |
| 11 | csShowCallStatus(status) |   | Sự kiện này xảy ra khi trạng thái cuộc gọi bị thay đổi |
| 12 | csCustomerAccept() |   | Sự kiện này được gọi khi khách hàng nghe cuộc gọi đối với cuộc gọi ra |
| 13 | csShowDeviceType(type) |   | Sự kiện này được gọi khi thông tin loại thiết bị đang dùng để tiếp nhận cuộc gọi thay đổi (1: gọi bằng trình duyệt, 2: nhận cuộc gọi qua IP phone nhưng phải đăng nhập vào mới tiếp nhận được, 4: nhận cuộc gọi qua IP phone nhưng không cần đăng nhập vào) |
| 14 | csCurrentCallId(callId) |   | Sự kiện xảy ra khi có cuộc gọi đang diễn ra và trả về callId của cuộc gọi |
| 15 | csInitError(errorCode) |   | Sự kiện này xảy ra khi thiết lập thông số không thành công và sẽ trả về mã lỗi(token lỗi, lỗi kết nối,…) |
| 16 | csInitComplete() |   | Sự kiện được gọi khi thiết lập thành công mọi thông số và đã sẵn sang để bắt đầu nhận hay tiếp nhận cuôc gọi **Lưu ý** : Trong trường hợp muốn kích hoạt thoại luôn thì sẽ gọi hàm **csEnableCall()** trong hàm này |
| 17 | csListTransferAgent(listTransferAgent) |   | Sự kiện được gọi khi lấy thành công list agent đang online |
| 18 | csTransferCallError(error, tranferedAgentInfo) |   | Sự kiện được gọi khi chuyển cuộc gọi thất bại. `error` là mã lỗi trả về, `tranferedAgentInfo` là thông tin agent được chuyển (có thể null) |
| 19 | csTransferCallSuccess(tranferedAgentInfo) |   | Sự kiện được gọi khi chuyển cuộc gọi thành công. `tranferedAgentInfo` là thông tin agent được chuyển |
| 20 | csNewCallTransferRequest(transferCall) |   | Sự kiện được gọi khi có một cuộc gọi được chuyển  |
| 21 | csTransferCallResponse(status) |   | Sự kiện được gọi khi người được chuyển cuộc gọi bấm từ chối hoặc tiếp nhận yêu cầu. `status=OK` tiếp nhận,`status=NOK` từ chối  |

**Các hàm này đã được để trong file custom.js**

Phụ lục: Bảng mã lỗi khi gọi ra

| STT | Error Code | Sip Code | Mô tả |
| --- | --- | --- | --- |
| 1 | IPCC\_NOT\_CONNECT\_CUSTOMER | 404 | Không kết nối được đến số thuê bao |
| 2 | 486 | Khách hàng đang bận |
| 3 | 408 | Khách hàng không nghe máy hoặc có lỗi xảy ra |
| 4 | 403 | Đầu số này chưa được thanh toán cước |
| 5 | 487 | Khách hàng không nghe máy |
| 6 | 480 | Thuê bao khách hàng tạm thời không liên lạc được |
| 7 | Các mã khác | Có lỗi xảy ra |
| 8 | IPCC\_NOT\_CONNECT\_AGENT\_ID |   | Không thể kết nối được đến thiết bị nghe gọi của tư vấn viênkiểm tra lại IP Phone/SoftPhone và đường truyền Internet |
| 9 | CALLOUT\_AGENT\_BUSY |   | Mã trả về 786 - Busy. Vui lòng đăng xuất và đăng nhập lại để tiếp tục sử dụng. |
| 10 | CALLOUT\_PERMISSION\_DENY |   | Không có quyền gọi ra. Liên hệ Admin để cấu hình |
| 11 | ERROR\_CALLOUT\_CONNECT |   | Có lỗi xảy ra |

Bảng mã lỗi khi chuyển cuộc gọi

| STT | Error Code | Sip Code | Mô tả |
| --- | --- | --- | --- |
| 1 | NOT\_CHOOSE\_TYPE\_TRANSFER | | Chưa chọn loại chuyển cuộc gọi |
| 2 | TRANSFER\_CALL\_FAILED | | Chuyển cuộc gọi thất bại |
| 3 | NOT\_IN\_A\_CALL | | Không trong cuộc gọi |
| 4 | COULD\_NOT\_GET\_CALL\_INFO | | Không thể lấy thông tin cuộc gọi |
