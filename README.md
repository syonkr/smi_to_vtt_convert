# smi to vtt
Convert `.smi` to `.vtt` with stylesheet

## Usage

index.html
``` html
<link rel="stylesheet" type="text/css" href="./convert_vtt_color.php?name=movie.smi">

<video controls="" id="video" name="media" controlslist="nodownload">
    <source src="movie.mp4" type="video/mp4">
    <track kind="subtitles" srclang="ko" label="Korean" src="./convert_vtt.php?name=movie.smi" default>
</video>
```