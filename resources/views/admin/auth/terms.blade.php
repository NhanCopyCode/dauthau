@if(isset($web) && $web)
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Terms and conditions</title>
    </head>
    <body>
@endif
<!-- Terms and conditions modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="Terms and conditions" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Điều khoản và điều kiện</h3>
            </div>

            <div class="modal-body">
                <p><b>Mục đích thu thập thông tin</b></p>

                <p>Để truy cập và sử dụng một số dịch vụ tại website, bạn có thể sẽ được yêu cầu đăng ký với chúng tôi thông tin cá nhân (Email, Họ tên, Số ĐT liên lạc…). Mọi thông tin khai báo phải đảm bảo tính chính xác và hợp pháp. Chúng không chịu mọi trách nhiệm liên quan đến pháp luật của thông tin khai báo.</p>

                <p><b>Phạm vi sử dụng thông tin</b></p>

                <p>Chúng tôi thu thập và sử dụng thông tin cá nhân bạn với mục đích phù hợp và hoàn toàn tuân thủ nội dung của “Chính sách bảo mật” này.  Khi cần thiết, chúng tôi có thể sử dụng những thông tin này để liên hệ trực tiếp với bạn dưới các hình thức như: Gởi thư ngỏ, đơn đặt hàng, thư cảm ơn, thông tin về kỹ thuật và bảo mật…</p>

                <p>Huesoft cam kết không chia sẻ thông tin khách hàng cho bất kỳ bên thứ 3 nào ngoại trừ khi có yêu cầu của cơ quan có thẩm quyền của nhà nước.</p>

                <p><b>Thời gian lưu trữ thông tin</b></p>

                <p>Thông tin của Quý khách hàng là dữ liệu đầu vào quan trọng để Huesoft cung cấp và quản lý quyền sử dụng các sản phẩm, dịch vụ phần mềm, vì thế thông tin khách hàng sẽ được lưu trữ trong suốt quá trình hoạt động của Huesoft.</p>

                <p><b>Địa chỉ của đơn vị thu thập và quản lý thông tin cá nhân</b></p>

                <p>Công ty cổ phần Phần mềm và Thương mại điện tử Huế</p>

                <p>Địa chỉ: 06 Lê Lợi, phường Vĩnh Ninh, thành phố Huế, tỉnh Thừa Thiên Huế</p>

                <p>Điện thoại: 0234.3822725</p>

                <p>Email: contact@huesoft.com.vn</p>

                <p><b>Phương tiện và công cụ để người dùng tiếp cận và chỉnh sửa dữ liệu cá nhân của mình</b></p>

                <p>Quý khách có quyền liên hệ với chúng tôi bằng cách gửi email tới contact@huesoft.com.vn để được tiếp cận, xóa bỏ và (hoặc) chỉnh sửa, cập nhật dữ liệu cá nhân của mình.</p>

                <p><b>Cam kết bảo mật thông tin cá nhân khách hàng</b></p>

                <p>Huesoft cam kết luôn nỗ lực sử dụng những biện pháp tốt nhất để bảo mật thông tin của khách hàng nhằm đảm bảo những thông tin này không bị đánh cắp, tiết lộ ngoài ý muốn.</p>
                <p>Huesoft cam kết không chia sẻ, bán hoặc cho thuê thông tin của khách hàng với bất kỳ bên thứ ba nào.</p>
                <p><b>Thay đổi về chính sách</b></p>

                <p>Nội dung của “Chính sách bảo mật” này có thể thay đổi để phù hợp với các nhu cầu của chúng tôi cũng như nhu cầu và sự phản hồi từ khách hàng nếu có. Khi cập nhật nội dung chính sách này, chúng tôi sẽ chỉnh sửa lại thời gian “Cập nhật lần cuối” bên trên.</p>
                <p>Nội dung “Chính sách bảo mật” này chỉ áp dụng tại website của chúng tôi, không bao gồm hoặc liên quan đến các bên thứ ba đặt quảng cáo. Do đó, chúng tôi đề nghị bạn đọc và tham khảo kỹ nội dung “Chính sách bảo mật” của từng website mà bạn đang truy cập.</p>
            </div>
            @if(!isset($web) || !$web)
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            @endif
        </div>
    </div>
</div>
@if(isset($web) && $web)
    </body>
    </html>
@endif