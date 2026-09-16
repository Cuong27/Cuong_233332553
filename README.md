# C++ Programming Exercises Web

Website dùng để hiển thị và chạy các bài tập C++ từ **Bài 1 đến Bài 7** qua giao diện web cho bài tập mã nguồn mở

Project sử dụng:

* HTML
* CSS
* PHP
* C++

## Cấu trúc project

```text
Cuong_233332553/
├── README.md
├── Dockerfile.vercel
├── Caddyfile
│
├── UI_UX/
│   ├── index.html
│   ├── bai.php
│   └── style.css
│
└── Bai_tap/
    ├── Bai_1.cpp
    ├── Bai_2.cpp
    ├── Bai_3.cpp
    ├── Bai_4.cpp
    ├── Bai_5.cpp
    ├── Bai_6.cpp
    └── Bai_7.cpp
```

## Chức năng

* Trang chủ hiển thị danh sách Bài 1 đến Bài 7.
* Hiển thị source code C++ của từng bài.
* Cho phép nhập dữ liệu đầu vào.
* Compile C++ bằng `g++`.
* Chạy chương trình trực tiếp từ giao diện Web.
* Hiển thị output của chương trình.
* Có giới hạn thời gian chạy để tránh chương trình chạy vô hạn.

## Danh sách bài tập

### Bài 1

Nhập số liên tục cho đến khi nhập số `0` thì dừng.

### Bài 2

Sử dụng hàm kiểm tra một số có phải số hoàn hảo hay không.

### Bài 3

Viết hàm tính giai thừa:

```text
n! = n × (n - 1)!
```

### Bài 4

Liệt kê tất cả các ước số của số nguyên dương `n`.

### Bài 5

Khởi tạo một mảng số nguyên gồm 10 phần tử. Đếm và in ra các phần tử có giá trị âm, giá trị dương.

### Bài 6

Viết chương trình nhập vào một số giây sau đó in ra màn hình thời gian dưới dạng giờ:phút:giây

### Bài 7

Class PERSON gồm các thông tin: Họ tên, ngày sinh, quê quán.
Xây dựng class SINHVIEN kế thừa từ class trên và có thêm thuộc tính lớp.
Tạo đối tượng sinh viên và in thông tin ra màn hình giao diện là thông tin cá
nhân của sinh viên.

# Lưu ý

Project không sử dụng localhost hay XAMPP, thay vào đó sử dụng vercel, nền tảng đám mây cho phép deploy dự án mà không cần lo đến việc quản lý server phức tạp






