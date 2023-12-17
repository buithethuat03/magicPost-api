# MAGICPOST

<p ><b ><span
>2. Mô tả nghiệp
vụ</span></b></p>

<p class="n">
	MagicPost là công ty hoạt động trong lĩnh vực chuyển phát. Công ty này có các điểm giao dịch phủ khắp cả nước. Mỗi điểm giao dịch phụ trách một vùng. Ngoài các điểm giao dịch, công ty cũng có nhiều điểm tập kết hàng hóa. Mỗi điểm giao dịch sẽ làm việc với một điểm tập kết. Ngược lại, một điểm tập kết sẽ làm việc với nhiều điểm giao dịch.
</p>
<p >
	Người gửi, có hàng cần gửi, đem hàng đến một điểm giao dịch (thường là gần nhất) để gửi. Hàng, sau đó, được đưa đến điểm tập kết ứng với điểm giao dịch của người gửi, rồi được chuyển đến điểm tập kết ứng với điểm giao dịch của người nhận. Tại điểm giao dịch của người nhận, nhân viên giao hàng sẽ chuyển hàng đến tận tay người nhận.
</p>
<p>
	Công ty cần phát triển một phần mềm nhằm quản lý hệ thống chuyển phát nêu trên. Yêu cầu chức năng cho từng đối tượng sử dụng như sau:
</p>


<h4>Chức năng cho lãnh đạo công ty</h4>
	<ul>
		<li>Quản lý hệ thống các điểm giao dịch và điểm tập kết.</li>
		<li>Quản lý tài khoản trưởng điểm điểm tập kết và điểm giao dịch. Mỗi điểm giao dịch hoặc điểm tập kết có một tài khoản trưởng điểm.</li> 
		<li>Thống kê hàng gửi, hàng nhận trên toàn quốc, từng điểm giao dịch hoặc điểm tập kết.</li>
	</ul>
	
<h4>Chức năng cho trưởng điểm tại điểm giao dịch</h4>
<ul>
		<li>Cấp tài khoản cho giao dịch viên tại điểm giao dịch.</li>
		<li>Thống kê hàng gửi, hàng nhận tại điểm giao dịch.</li>
</ul>
<h4>Chức năng cho giao dịch viên tại điểm giao dịch</h4>
	<ul>
		<li>Ghi nhận hàng cần gửi của khách (người gửi), in giấy biên nhận chuyển phát và phát cho khách hàng (tham khảo Hình 1 trong phụ lục).</li>
		<li>Tạo đơn chuyển hàng gửi đến điểm tập kết mỗi/trước khi đem hàng gửi đến điểm tập kết.</li>
		<li>Xác nhận (đơn) hàng về từ điểm tập kết.</li>
		<li>Tạo đơn hàng cần chuyển đến tay người nhận.</li>
		<li>Xác nhận hàng đã chuyển đến tay người nhận theo .</li>
		<li>Xác nhận hàng không chuyển được đến người nhận và trả lại điểm giao dịch.</li>
		<li>Thống kê các hàng đã chuyển thành công, các hàng chuyển không thành công và trả lại điểm giao dịch.</li>
	</ul>
	

<h4>Chức năng cho trưởng điểm tại điểm tập kết</h4>
	<ul>
		<li>Quản lý tài khoản nhân viên tại điểm tập kết.</li>
		<li>Thống kê hàng đi, đến.</li>
	</ul>
	
<h4>Chức năng cho nhân viên tại điểm tập kết</h4>
	<ul>
		<li>Xác nhận (đơn) hàng đi từ điểm giao dịch chuyển đến.</li>
		<li>Tạo đơn chuyển hàng đến điểm tập kết đích (ứng với điểm giao dịch đích, tức điểm giao dịch phụ trách vùng ứng với địa chỉ của người nhận).</li>
		<li>Xác nhận (đơn) hàng nhận về từ điểm tập kết khác.</li>
		<li>Tạo đơn chuyển hàng đến điểm giao dịch đích.</li>
	</ul>
	
	
<h4>Chức năng cho khách hàng</h4>
	<ul>
		<li>Tra cứu trạng thái và tiến trình chuyển phát của kiện hàng mình gửi.</li>
	</ul>
<h4>Yêu cầu</h4>
<ul>
<li>Đã tải php, composer, xampp</li>
</ul>
<h4>Cách chạy backend:</h4>
<ul>
<li>Bật xampp, mySQL</li>
<li>php artisan migrate</li>
<li>php artisan db:seed</li>
<li>php artisan serve</li>
</ul>


