extern crate rand;
use rand::distributions::{Alphanumeric, DistString};
use std::env;

fn main() {
    let args: Vec<String> = env::args().collect();
    let qty = &args[2];
    let length = &args[1];

    println!("Quantity will be {}", qty);
    println!("Length of strings: {}", length);

    for _i in 1..10 {
        let string = Alphanumeric.sample_string(&mut rand::thread_rng(), 20);
        println!("{}", string);
    }
}