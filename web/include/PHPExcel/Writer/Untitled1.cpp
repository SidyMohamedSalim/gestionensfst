#include<stdlib.h>
#include<stdio.h>

main(){
	
	int x=10;
	int *p;
	int*Q;
	Q=(int*)malloc(sizeof(int));
	p=Q;
	printf( "x=%d\n",x);
	printf( "&X=%d\n",&x);
	
	printf( "p=%d\n",p);
	
	printf( "Q=%d\n",Q);
	
	
	
	
	system("pause");
}
