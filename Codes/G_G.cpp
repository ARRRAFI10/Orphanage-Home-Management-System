#include<bits/stdc++.h>
using namespace std;
#define endl "\n"
#define int long long

signed main(){
    int h,m;
    cin>>h>>m;
    if(m>=45){
        m=m-45;
        cout<<h<<" "<<m;
    }
    else{
        if(h==0) h=23;
        else{
            h=h-1;
            
        }
        m=60-(45-m);
        cout<<h<<" "<<m;
    }
}
