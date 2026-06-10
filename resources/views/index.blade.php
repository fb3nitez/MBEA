<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    <aside class="drawer lg:drawer-open fixed top-0 bottom-0">
      <input id="my-drawer-3" type="checkbox" class="drawer-toggle" />
      <div class="drawer-content flex flex-col items-center justify-center">
        <!-- Page content here -->
        <label for="my-drawer-3" class="btn drawer-button lg:hidden">
          Open drawer
        </label>
      </div>
      <div class="drawer-side w-72 shadow-sm">
        <label for="my-drawer-3" aria-label="close sidebar" class="drawer-overlay"></label>

        <div class="px-5 py-5 flex align-middle gap-2 border-b border-base-300">
          <div class="flex flex-col">
            <span class="font-bold text-xl">MBEA</span>
            <span class="text-lg mt-1">sub</span>
          </div>
        </div>

        <ul class="menu w-80 p-4">
          <!-- Sidebar content here -->
          <li><a>Sidebar Item 1</a></li>
          <li><a>Sidebar Item 2</a></li>
        </ul>
      </div>
    </aside>

    <nav class="navbar bg-base-100 shadow-sm sticky top-0 ms-72">
        <div class="flex-1">
            <a class="btn btn-ghost text-xl">Dashboard</a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1">
                <li><a>Link</a></li>
            </ul>
        </div>
        <div class="flex ms-5">
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img alt="Tailwind CSS Navbar component"
                            src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                    </div>
                </div>
                <ul tabindex="-1"
                    class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                    <li>
                        <a class="justify-between">
                            Profile
                            <span class="badge">New</span>
                        </a>
                    </li>
                    <li><a>Settings</a></li>
                    <li><a>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>


    <main class="ms-72 p-3">
        <h1>Main Content</h1>
    </main>
</body>

</html>
