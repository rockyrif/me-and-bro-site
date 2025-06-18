import { createBrowserRouter } from "react-router";
import TheGuestLayout from "../layouts/TheGuestLayout";
import HomePage from "../pages/HomePage";
import AboutPage from "../pages/AboutPage";

const router = createBrowserRouter([
  {
    path: "/",
    Component: TheGuestLayout,
    children: [
      {
        path: "/",
        Component: HomePage,
      },
      {
        path: "/about",
        Component: AboutPage,
      },
    ],
  },
]);

export default router;
