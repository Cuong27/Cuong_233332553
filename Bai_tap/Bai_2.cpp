#include <iostream>

using namespace std;

bool So_hoan_hao(unsigned int x){
    
    if (x < 2)
        return false;

    unsigned int sum = 1;

    for (unsigned int i = 2; i * i <= x; i++){
        
        if (x % i == 0){
            sum += i;

            if (i != x / i)
                sum += x / i;
        }
    }

    return x == sum;
}

int main(){
    
    int input;
    cin >> input;

    if (So_hoan_hao(input)){

        cout << "Perfect number: " << input << '\n';
        return 0;

    }
    else{ 
        
        cout << "Non perfect number: " << input << '\n';
        return 0;
    }
}