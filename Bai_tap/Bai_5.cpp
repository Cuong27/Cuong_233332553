#include <iostream>

using namespace std;

int main(){

    int a[10];

    int Pos = 0;
    int Neg = 0;


    for (int i = 0; i < 10; i++){

        cin >> a[i];
    
    }

    cout << "\nPos : ";

    for (int i = 0; i < 10; i++){

        if (a[i] > 0){

            cout << a[i] << " ";
            Pos++;
        
        }
    }

    cout << "\nNeg: ";

    for (int i = 0; i < 10; i++){

        if (a[i] < 0){

            cout << a[i] << " ";
            Neg++;
        
        }
    }

    cout << "\n\nSo phan tu duong: " << Pos;
    cout << "\nSo phan tu am: " << Neg << endl;

    return 0;
}