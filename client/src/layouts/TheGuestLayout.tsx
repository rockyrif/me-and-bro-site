import React from "react";
import TheNavbar from "../components/TheNavbar";
import { Outlet } from "react-router";

const TheGuestLayout = () => {
  return (
    <div>
      <TheNavbar />
      <Outlet />
    </div>
  );
};

export default TheGuestLayout;
