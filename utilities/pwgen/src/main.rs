extern crate rand;
use rand::distributions::{Alphanumeric, DistString};

fn main() {
    for _i in 1..10 {
        let string = Alphanumeric.sample_string(&mut rand::thread_rng(), 20);
        println!("{}", string);
    }
}