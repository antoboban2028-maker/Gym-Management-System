import java.util.*;
public class Q11 {
public static void main(String[] args) {
HashMap<Integer,String> hm = new HashMap<>();
hm.put(3,"C");
hm.put(1,"A");
hm.put(2,"B");
TreeMap<Integer,String> tm = new TreeMap<>(hm);
System.out.println(tm);
}
}
