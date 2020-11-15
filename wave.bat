for %%a in (*.wav) do (
	wav2png -w 300 -h 32 -b ffffffff -f ccccccff -o mini%%a.png %%a
	wav2png -w 800 -h 256 -b ffffff00 -f 0000bbff %%a
	wav2png -w 25000 -h 150 -b ffffff00 -f 0000bbff -o dj24%%a.png %%a
)