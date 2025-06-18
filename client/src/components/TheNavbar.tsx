import React from "react";
import { Link } from "react-router";

const TheNavbar = () => {
  return (
    <div>
      <Link to={"/"}>Home</Link>
      <Link to={"/about"}>About</Link>
    </div>
  );
};

export default TheNavbar;
