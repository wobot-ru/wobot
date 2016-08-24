/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function countDays(date1,date2)
{
    return Math.ceil((date2.getTime()-date1.getTime())/(1000*60*60*24));
}

function StrToDate(Dat) {
   var year=Number(Dat.split(".")[2]);
   var month=Number(Dat.split(".")[1])-1;
   var day=Number(Dat.split(".")[0]);
   var dat= new Date(year,month,day);
   return dat;
}

function DateToStr(Dat) {
   var year=Dat.getFullYear();
   var month=Dat.getMonth()+1;
   var day=Dat.getDate();
   var str;
   if (month<10) month ='0'+month;
   if (day<10)
        day='0'+day.toString();
   return day+"."+month+"."+year;
}

function sortCbRaiting(a,b)
 {
    return b.num - a.num;
 }
 
 function sortCbNames(a,b)
 {
      if(a.name<b.name)
         return -1 // Или любое число, меньшее нуля
      if(a.name>b.name)
         return 1  // Или любое число, большее нуля
      // в случае а = b вернуть 0
      return 0
 }