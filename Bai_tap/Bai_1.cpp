#include <iostream>

using namespace std;

int main(){

    float n;

    while (true){

        if (!(cin >> n)){
            break;
        }

        if (n == 0){
            cout<<"Dung chuong trinh";
            break;
        }

        cout << "Number: " << n << endl;
    }

    return 0;
}