#include <iostream>
#include <string>

using namespace std;

class PERSON {

    protected:
    string hoTen;
    string ngaySinh;
    string queQuan;

public:
    void nhapPerson(){

        getline(cin, hoTen);

        getline(cin, ngaySinh);

        getline(cin, queQuan);
    
    }

    void xuatPerson(){

        cout << "Ho ten: " << hoTen << endl;
        cout << "Ngay sinh: " << ngaySinh << endl;
        cout << "Que quan: " << queQuan << endl;
    
    }
};

class SINHVIEN : public PERSON {

    private:
    string lop;

    public:
        void nhapSinhVien(){

            nhapPerson();

            getline(cin, lop);
    
        }

        void xuatSinhVien(){

            cout << "\n----------THONG TIN SINH VIEN----------\n";

            xuatPerson();

            cout << "Lop: " << lop << endl;
    }
};

int main()
{
    SINHVIEN sv;

    sv.nhapSinhVien();

    sv.xuatSinhVien();

    return 0;
}