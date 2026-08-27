<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
   <link rel="icon" href="{{ asset('ALS_logo.png') }}">
    <style>
      color {
        color: #61892f;
        color: #86c232;
        color: #222629;
        color: #474b4f;
        color: #6b6e70;
      }

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", "sans-serif";
      }

      body {
        background-color: #222622;
      }

      a {
        color: white;
        text-decoration: none;
      }

      .navbar {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        padding: 25px 9%;
        align-items: center;
        justify-content: space-between;
        display: flex;
        z-index: 100;
      }

      .navbar .logo {
        box-shadow: 0 0 10px #86c232;
        border-radius: 50%;
        padding: 5px;
        font-size: 30px;
        font-weight: 700;
        color: #86c232;
      }

      .navbar ul {
        display: flex;
      }

      .navbar ul li {
        list-style: none;
        margin-left: 35px;
      }

      .navbar ul li a {
        font-size: 20px;
        font-weight: 500;
        transition: 0.5s;
      }

      .navbar ul li:hover a,
      .navbar ul li.active a {
        color: #86c232;
        animation: glow-pulse-active 1s ease-in-out infinite alternate;
      }

      @keyframes glow-pulse-active {
        0% {
          text-shadow: 0 0 30px #a1f033;
        }
        100% {
          text-shadow: 0 0 10px #86c232;
          color: #fff;
        }
      }

      .home {
        display: flex;
        align-items: center;
        height: 100vh;
        padding: 60px 9% 0;
        color: #fff;
        gap: 60px;
      }

      strong {
        color: #86c232;
      }

      @keyframes glow-pulse {
        0% {
          text-shadow: 0 0 30px #a1f033;
          color: #ceeba7;
        }
        100% {
          text-shadow: 0 0 10px #86c232;
        }
      }

      .home-info h1 {
        font-size: 45px;
        font-weight: 700;
        transition: 1.5s;
        animation: glow-pulse 1.0s ease-in-out infinite alternate;
      }

      @keyframes slide-in {
        0% {
          transform: translateX(-150%);
        }
        100% {
          transform: translateX(0);
        }
      }

      .home-info h1:hover {
        text-shadow: 0 0 10px #86c232;
        transform: translateY(-10px);
      }

      .home-info h2 {
        font-size: 25px;
        margin-top: -5px;
        color: #86c232;
        animation: slide-in 0.5s forwards;
      }

      .home-info p {
        font-size: 20px;
        margin: 35px 0 50px;
        animation: slide-in 0.5s forwards;
      }

      .home-info .btn {
        background-color: #86c232;
        border: 2px solid #86c232;
        box-shadow: 0 0 10px #86c232;
        width: fit-content;
        padding: 15px;
        border-radius: 25px;
        transition: 0.5s;   
        animation: slide-in 0.5s forwards;
      }

      .home-info .btn a {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
      }

      .home-info .btn:hover {
        background: transparent;
        box-shadow: none;
      }

      .home-info .btn a:hover {
        text-shadow: 0 0 10px #86c232;
        color: #86c232;
      }

      hr {
        animation: slide-in 0.5s forwards;
      }

      @keyframes glow-pulse-box {
        0% {
          box-shadow: 0 0 30px #86c232;
          transform: translateY(-15px);
        }
        100% {
          box-shadow: 0 0 10px #6b6e70;
        }
      }

      .home-img .img-box {
        width: 32vw;
        height: 32vw;
        background: #86c232;
        border-radius: 50%;
        padding: 5px;
        border: 2px solid #61892f;
        box-shadow: 0 0 10px #61892f;
        animation: glow-pulse-box 1s ease-in-out infinite alternate;
      }

      .home-img .img-box .img-item {
        position: relative;
        width: 100%;
        height: 100%;
        background: slategray;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        overflow: hidden;
      }

      .home-img .img-box .img-item img {
        position: absolute;
        display: block;
        width: 80%;
        object-fit: cover;
      }
    </style>
  </head>
  <body>
    <nav class="navbar">
      <a href="#" class="logo">AGS</a>
      <ul>
        <li class="active"><a href="index.html".html">Home</a></li>
        <li><a href="aboutMe.html">About</a></li>
        <li><a href="skill.html">Skills</a></li>
        <li><a href="project.html">Projects</a></li>
        <li><a href="contact.html">Contact</a></li>
      </ul>
    </nav>
    <section class="home">
      <div class="home-info">
        <h1>Adrian Santiago</h1>
        <h2>I'm a Fullstack Developer / Penetration Tester</h2>
        <hr />
        <p>
          I'm a passionate programmer who loves building modern web apps. I
          specialize in <strong>React, Node.js, Express, redis, mongoDb, MySQL</strong>. My
          focus is on creating clean, scalable, and efficient solutions. I enjoy
          solving problems, learning new tools, and experimenting with
          technologies.
        </p>
        <div class="btn">
          <a href="aboutMe.html">More About Me</a>
        </div>
      </div>

      <div class="home-img">
        <div class="img-box">
          <div class="img-item">
            <img src="{{ asset('resume_pict.jpg') }}" alt="Profile Picture">
          </div>
        </div>
      </div>
    </section>
  </body>
</html>