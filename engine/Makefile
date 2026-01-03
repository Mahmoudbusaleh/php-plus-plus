# Compiler settings
CC = g++
CFLAGS = -fPIC -shared -O3

# Target library name
TARGET = router.so

all:
	$(CC) $(CFLAGS) -o $(TARGET) router.cpp

clean:
	rm -f $(TARGET)
