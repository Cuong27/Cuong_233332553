#include <iostream>
#include <iomanip>

using namespace std;

int main(){

    unsigned int totalSeconds;

    cin >> totalSeconds;

    unsigned int gio;
    unsigned int phut;
    unsigned int giay;

    gio = totalSeconds / 3600;

    phut = (totalSeconds % 3600) / 60;

    giay = totalSeconds % 60;

    cout << setfill('0');

    cout << setw(2) << gio << ":"
         << setw(2) << phut << ":"
         << setw(2) << giay << endl;

    return 0;

}