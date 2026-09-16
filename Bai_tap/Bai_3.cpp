#include<iostream>

using namespace std;

long long Factorial(int x){
    if(x == 0 || x == 1) return 1;
    return x*Factorial(x-1);
}

int main(){

    int input;

    cin >> input;

    cout<<"Factorial of input: "<<Factorial(input)<<endl;
    return 0;

}