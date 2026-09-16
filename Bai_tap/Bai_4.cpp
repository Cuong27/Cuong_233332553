#include <iostream>

using namespace std;

int main(){

    unsigned int n;
    cin >> n;

    cout << "Ouput: ";

    for (unsigned int i = 1; i <= n; i++){

        if (n % i == 0){

            cout << i << " ";
        
        }
    }

    cout << endl;

    return 0;
}