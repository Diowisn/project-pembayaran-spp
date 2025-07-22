from threading import Thread, Lock
import sys
import time
import os

lock = Lock()

lyrics = [
    (0.3, "Do you think I have forgotten?"),
    (5.0, "Do you think I have forgotten?"),
    (10.0, "Do you think I have forgotten"),
    (15.3, "about you?"),
    (20.0, ""),
    (20.0, "There was something about you that now I can't remember"),
    (25.3, "It's the same damn thing that made my heart surrender"),
    (30.0, "And I'll miss you on a train, I'll miss you in the morning"),
    (35.0, "I never know what to think about,"),
    (40.3, "I think about you"),
    (45.0, "(about you)"),
]

def clear_screen():
    os.system("cls" if os.name == "nt" else "clear")

def typewriter(text, typing_speed=0.1):
    with lock:
        for char in text:
            sys.stdout.write(char)
            sys.stdout.flush()
            time.sleep(typing_speed)
        print()

def play_lyrics(lyrics, typing_speed=0.08, offset_delay=0.035):
    start_time = time.time()
    for timestamp, line in lyrics:
        current_time = time.time()
        sleep_time = (timestamp + offset_delay) - (current_time - start_time)
        if sleep_time > 0:
            time.sleep(sleep_time)

        thread = Thread(target=typewriter, args=(line, typing_speed))
        thread.start()
        thread.join()

if __name__ == "__main__":
    clear_screen()
    time.sleep(1)

    play_lyrics(lyrics, typing_speed=0.08, offset_delay=0.035)