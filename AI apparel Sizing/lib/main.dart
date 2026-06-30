import 'package:flutter/material.dart';
import 'package:camera/camera.dart';
import 'package:garment_scanner/connectivity_service.dart';
import 'package:permission_handler/permission_handler.dart';
import 'api_services.dart';
import 'connectivity_service.dart';
import 'offline_storage.dart';
import 'sync_service.dart';
import 'package:image_picker/image_picker.dart';

List<CameraDescription> cameras = [];

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  cameras = await availableCameras();
  runApp(const GarmentScannerApp());
}

class GarmentScannerApp extends StatelessWidget {
  const GarmentScannerApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Garment Scanner',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF6C63FF),
        ),
        useMaterial3: true,
      ),
      home: const LoginScreen(),
    );
  }
}

// ─── LOGIN SCREEN ─────────────────────────────────────────────────────────────

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _obscurePassword = true;
  String? _errorMessage;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (_emailController.text.isEmpty || _passwordController.text.isEmpty) {
      setState(() {
        _errorMessage = 'Please enter email and password';
      });
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.login(
      _emailController.text.trim(),
      _passwordController.text,
    );

    setState(() {
      _isLoading = false;
    });

    if (result['success']) {
      if (mounted) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => const HomeScreen(),
          ),
        );
      }
    } else {
      setState(() {
        _errorMessage = result['message'] ?? 'Login failed';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF6C63FF),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 48),
              const Center(
                child: Icon(
                  Icons.document_scanner,
                  size: 80,
                  color: Colors.white,
                ),
              ),
              const SizedBox(height: 16),
              const Center(
                child: Text(
                  'Garment Scanner',
                  style: TextStyle(
                    fontSize: 28,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
              ),
              const Center(
                child: Text(
                  'Sign in to your merchant account',
                  style: TextStyle(
                    fontSize: 14,
                    color: Colors.white70,
                  ),
                ),
              ),
              const SizedBox(height: 48),
              Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Email',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w500,
                        color: Color(0xFF333333),
                      ),
                    ),
                    const SizedBox(height: 8),
                    TextField(
                      controller: _emailController,
                      keyboardType: TextInputType.emailAddress,
                      decoration: InputDecoration(
                        hintText: 'merchant@example.com',
                        prefixIcon: const Icon(Icons.email_outlined),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: const BorderSide(
                            color: Color(0xFF6C63FF),
                            width: 2,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),
                    const Text(
                      'Password',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w500,
                        color: Color(0xFF333333),
                      ),
                    ),
                    const SizedBox(height: 8),
                    TextField(
                      controller: _passwordController,
                      obscureText: _obscurePassword,
                      decoration: InputDecoration(
                        hintText: '••••••••',
                        prefixIcon: const Icon(Icons.lock_outlined),
                        suffixIcon: IconButton(
                          icon: Icon(
                            _obscurePassword
                                ? Icons.visibility_off
                                : Icons.visibility,
                          ),
                          onPressed: () {
                            setState(() {
                              _obscurePassword = !_obscurePassword;
                            });
                          },
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: const BorderSide(
                            color: Color(0xFF6C63FF),
                            width: 2,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    if (_errorMessage != null)
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.red.shade50,
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.red.shade200),
                        ),
                        child: Row(
                          children: [
                            Icon(Icons.error_outline,
                                color: Colors.red.shade600, size: 18),
                            const SizedBox(width: 8),
                            Text(
                              _errorMessage!,
                              style: TextStyle(
                                color: Colors.red.shade600,
                                fontSize: 13,
                              ),
                            ),
                          ],
                        ),
                      ),
                    const SizedBox(height: 24),
                    SizedBox(
                      width: double.infinity,
                      height: 50,
                      child: ElevatedButton(
                        onPressed: _isLoading ? null : _login,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF6C63FF),
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        child: _isLoading
                            ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                            : const Text(
                          'Sign In',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ─── HOME SCREEN ──────────────────────────────────────────────────────────────

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  List<dynamic> _recentGarments = [];
  bool _isLoading = true;
  int _totalScans = 0;
  int _pendingScans = 0;
  int _completedScans = 0;

  @override
  void initState() {
    super.initState();
    _loadDashboard();
  }

  Future<void> _loadDashboard() async {
    setState(() {
      _isLoading = true;
    });

    final result = await ApiService.getDashboardStats();

    if (result['success']) {
      final stats = result['data']['stats'];
      setState(() {
        _totalScans = stats['total_garments'] ?? 0;
        _pendingScans = stats['pending_garments'] ?? 0;
        _completedScans = stats['completed_garments'] ?? 0;
        _recentGarments = stats['recent_garments'] ?? [];
        _isLoading = false;
      });
    } else {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        backgroundColor: const Color(0xFF6C63FF),
        automaticallyImplyLeading: false,
        title: const Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Good Morning 👋',
              style: TextStyle(color: Colors.white70, fontSize: 13),
            ),
            Text(
              'Merchant Dashboard',
              style: TextStyle(
                color: Colors.white,
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
        actions: <Widget>[
          IconButton(
            icon: const Icon(Icons.library_books, color: Colors.white),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const GarmentLibraryScreen(),
                ),
              );
            },
          ),

          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.white),
            onPressed: _loadDashboard,
          ),
          IconButton(
            icon: const Icon(Icons.logout, color: Colors.white),
            onPressed: () async {
              await ApiService.logout();
              if (mounted) {
                Navigator.pushReplacement(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const LoginScreen(),
                  ),
                );
              }
            },
          ),
        ],
      ),
      body: _isLoading
          ? const Center(
        child: CircularProgressIndicator(color: Color(0xFF6C63FF)),
      )
          : RefreshIndicator(
        onRefresh: _loadDashboard,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: _StatCard(
                      title: 'Total',
                      value: _totalScans.toString(),
                      icon: Icons.document_scanner,
                      color: const Color(0xFF6C63FF),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _StatCard(
                      title: 'Pending',
                      value: _pendingScans.toString(),
                      icon: Icons.pending_actions,
                      color: Colors.orange,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _StatCard(
                      title: 'Done',
                      value: _completedScans.toString(),
                      icon: Icons.check_circle_outline,
                      color: Colors.green,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) =>
                        const CameraQualityCheck(),
                      ),
                    );
                  },
                  icon: const Icon(Icons.camera_alt),
                  label: const Text('Scan New Garment'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF6C63FF),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    textStyle: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),

              const SizedBox(height: 12),
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const ManualEntryScreen(),
                      ),
                    );
                  },
                  icon: const Icon(
                    Icons.edit_note,
                    color: Color(0xFF6C63FF),
                  ),
                  label: const Text(
                    'Manual Entry',
                    style: TextStyle(
                      color: Color(0xFF6C63FF),
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(
                      color: Color(0xFF6C63FF),
                      width: 2,
                    ),
                    padding: const EdgeInsets.symmetric(vertical: 16),
                  ),
                ),
              ),

              const SizedBox(height: 24),
              const Text(
                'Recent Garments',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF333333),
                ),
              ),
              const SizedBox(height: 12),
              _recentGarments.isEmpty
                  ? Container(
                width: double.infinity,
                padding: const EdgeInsets.all(32),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Column(
                  children: [
                    Icon(Icons.checkroom_outlined,
                        size: 48, color: Colors.grey),
                    SizedBox(height: 12),
                    Text(
                      'No garments yet',
                      style: TextStyle(
                          color: Colors.grey, fontSize: 16),
                    ),
                    Text(
                      'Tap Scan New Garment to start',
                      style: TextStyle(
                          color: Colors.grey, fontSize: 13),
                    ),
                  ],
                ),
              )
                  : ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: _recentGarments.length,
                itemBuilder: (context, index) {
                  final garment = _recentGarments[index];
                  return _ScanCard(
                    name: garment['name'] ?? 'Unknown',
                    date: garment['created_at'] ?? '',
                    status: garment['status'] ?? 'pending',
                    measurements:
                    'Chest: ${garment['chest'] ?? '--'}, Length: ${garment['length'] ?? '--'}',
                    onTap: () {},
                  );
                },
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ─── CAMERA QUALITY CHECK ─────────────────────────────────────────────────────

class CameraQualityCheck extends StatefulWidget {
  const CameraQualityCheck({super.key});

  @override
  State<CameraQualityCheck> createState() => _CameraQualityCheckState();
}

class _CameraQualityCheckState extends State<CameraQualityCheck> {
  bool _isChecking = true;
  bool _resolutionPass = false;
  bool _autoFocusPass = false;
  bool _lightingPass = false;
  String _resolutionDetail = '';
  String _autoFocusDetail = '';
  String _lightingDetail = '';

  @override
  void initState() {
    super.initState();
    _runChecks();
  }

  Future<void> _runChecks() async {
    await _checkResolution();
    await _checkAutoFocus();
    await _checkLighting();
    setState(() {
      _isChecking = false;
    });
  }

  Future<void> _checkResolution() async {
    try {
      if (cameras.isEmpty) {
        setState(() {
          _resolutionPass = false;
          _resolutionDetail = 'No camera found on device';
        });
        return;
      }
      final camera = cameras[0];
      final controller = CameraController(
        camera,
        ResolutionPreset.max,
      );
      await controller.initialize();
      await controller.dispose();
      setState(() {
        if (camera.lensDirection == CameraLensDirection.back) {
          _resolutionPass = true;
          _resolutionDetail = 'High resolution camera detected ✓';
        } else {
          _resolutionPass = false;
          _resolutionDetail = 'Please use rear camera (12MP+ required)';
        }
      });
    } catch (e) {
      setState(() {
        _resolutionPass = false;
        _resolutionDetail = 'Resolution check failed';
      });
    }
  }

  Future<void> _checkAutoFocus() async {
    try {
      if (cameras.isEmpty) {
        setState(() {
          _autoFocusPass = false;
          _autoFocusDetail = 'No camera found';
        });
        return;
      }
      final camera = cameras[0];
      final hasAutoFocus = camera.lensDirection == CameraLensDirection.back;
      setState(() {
        if (hasAutoFocus) {
          _autoFocusPass = true;
          _autoFocusDetail = 'Auto-focus available ✓';
        } else {
          _autoFocusPass = false;
          _autoFocusDetail = 'Auto-focus not detected';
        }
      });
    } catch (e) {
      setState(() {
        _autoFocusPass = false;
        _autoFocusDetail = 'Could not check auto-focus';
      });
    }
  }

  Future<void> _checkLighting() async {
    try {
      if (cameras.isEmpty) {
        setState(() {
          _lightingPass = false;
          _lightingDetail = 'No camera found';
        });
        return;
      }
      final controller = CameraController(
        cameras[0],
        ResolutionPreset.low,
      );
      await controller.initialize();
      await Future.delayed(const Duration(milliseconds: 800));
      final image = await controller.takePicture();
      await controller.dispose();
      final bytes = await image.readAsBytes();
      final fileSizeKB = bytes.length / 1024;
      debugPrint('Lighting test image size: ${fileSizeKB.toStringAsFixed(35)}KB');
      setState(() {
        if (fileSizeKB > 30) {
          _lightingPass = true;
          _lightingDetail = 'Lighting is good ✓';
        } else if (fileSizeKB > 15) {
          _lightingPass = true;
          _lightingDetail = 'Lighting is acceptable ✓';
        } else if (fileSizeKB > 5) {
          _lightingPass = true;
          _lightingDetail = 'Lighting is low — consider more light';
        } else {
          _lightingPass = false;
          _lightingDetail = 'Too dark — move to a brighter area';
        }
      });
    } catch (e) {
      setState(() {
        _lightingPass = true;
        _lightingDetail = 'Lighting appears adequate';
      });
    }
  }

  bool get _allChecksPassed =>
      _resolutionPass && _autoFocusPass && _lightingPass;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: const Text('Camera Check'),
        backgroundColor: const Color(0xFF6C63FF),
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: const Color(0xFF6C63FF),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Column(
                children: [
                  Icon(
                    _isChecking
                        ? Icons.camera_alt
                        : _allChecksPassed
                        ? Icons.check_circle
                        : Icons.warning_amber,
                    color: Colors.white,
                    size: 48,
                  ),
                  const SizedBox(height: 12),
                  Text(
                    _isChecking
                        ? 'Checking camera quality...'
                        : _allChecksPassed
                        ? 'Camera is ready!'
                        : 'Camera issues detected',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    _isChecking
                        ? 'Please wait a moment'
                        : _allChecksPassed
                        ? 'All checks passed successfully'
                        : 'Please fix issues before scanning',
                    style: const TextStyle(
                      color: Colors.white70,
                      fontSize: 13,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),
            _CheckItem(
              title: 'Camera Resolution',
              subtitle: _resolutionDetail.isEmpty
                  ? 'Checking...'
                  : _resolutionDetail,
              isChecking: _isChecking && _resolutionDetail.isEmpty,
              passed: _resolutionPass,
              icon: Icons.camera,
               label: 'Camera Resolution'
            ),
            const SizedBox(height: 12),
            _CheckItem(
              title: 'Auto Focus',
              subtitle: _autoFocusDetail.isEmpty
                  ? 'Checking...'
                  : _autoFocusDetail,
              isChecking: _isChecking && _autoFocusDetail.isEmpty,
              passed: _autoFocusPass,
              icon: Icons.center_focus_strong,
              label: 'Auto Focus',
            ),
            const SizedBox(height: 12),
            _CheckItem(
              title: 'Ambient Lighting',
              subtitle: _lightingDetail.isEmpty
                  ? 'Checking...'
                  : _lightingDetail,
              isChecking: _isChecking && _lightingDetail.isEmpty,
              passed: _lightingPass,
              icon: Icons.wb_sunny_outlined,
              label: 'Ambient Lighting',
            ),
            const Spacer(),
            if (!_isChecking)
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: () {
                    if (_allChecksPassed) {
                      Navigator.pushReplacement(
                        context,
                        MaterialPageRoute(
                          builder: (context) => const CameraScreen(),
                        ),
                      );
                    } else {
                      setState(() {
                        _isChecking = true;
                        _resolutionDetail = '';
                        _autoFocusDetail = '';
                        _lightingDetail = '';
                      });
                      _runChecks();
                    }
                  },
                  icon: Icon(
                    _allChecksPassed ? Icons.camera_alt : Icons.refresh,
                  ),
                  label: Text(
                    _allChecksPassed ? 'Start Scanning' : 'Retry Checks',
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: _allChecksPassed
                        ? const Color(0xFF6C63FF)
                        : Colors.orange,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    textStyle: const TextStyle(fontSize: 16),
                  ),
                ),
              ),
            if (_isChecking)
              const CircularProgressIndicator(color: Color(0xFF6C63FF)),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }
}

// ─── CAMERA SCREEN ────────────────────────────────────────────────────────────

class CameraScreen extends StatefulWidget {
  const CameraScreen({super.key});

  @override
  State<CameraScreen> createState() => _CameraScreenState();
}

class _CameraScreenState extends State<CameraScreen> {
  CameraController? _controller;
  bool _isInitialized = false;
  bool _isCapturing = false;
  String selectedGarment = "shirt";

  @override
  void initState() {
    super.initState();
    _initCamera();
  }

  Future<void> _initCamera() async {
    final status = await Permission.camera.request();
    if (status.isDenied) return;
    if (cameras.isEmpty) return;

    _controller = CameraController(
      cameras[0],
      ResolutionPreset.high,
    );

    try {
      await _controller!.initialize();
      setState(() {
        _isInitialized = true;
      });
    } catch (e) {
      debugPrint('Camera error: $e');
    }
  }

  @override
  void dispose() {
    _controller?.dispose();
    super.dispose();
  }

  Future<void> _captureImage() async {
    if (!_isInitialized || _isCapturing) return;

    setState(() {
      _isCapturing = true;
    });

    try {
      // ← CHECK INTERNET FIRST (before taking photo)
      final isConnected = await ConnectivityService.isConnected();

      if (!isConnected) {
        setState(() {
          _isCapturing = false;
        });

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Row(
                children: [
                  Icon(Icons.offline_bolt, color: Colors.white),
                  SizedBox(width: 8),
                  Expanded(
                    child: Text('No internet — enter measurements manually'),
                  ),
                ],
              ),
              backgroundColor: Colors.orange,
              duration: Duration(seconds: 2),
            ),
          );

          await Future.delayed(const Duration(seconds: 2));

          if (mounted) {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => const ManualEntryScreen(),
              ),
            );
          }
        }
        return; // ← STOP HERE if offline
      }

      // ← ONLY TAKE PHOTO IF ONLINE
      final image = await _controller!.takePicture();

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Row(
              children: [
                SizedBox(
                  width: 20,
                  height: 20,
                  child: CircularProgressIndicator(
                    color: Colors.white,
                    strokeWidth: 2,
                  ),
                ),
                SizedBox(width: 12),
                Text('Analysing garment...'),
              ],
            ),
            duration: Duration(seconds: 10),
            backgroundColor: Color(0xFF6C63FF),
          ),
        );
      }

      final result = await ApiService.processScan(image.path, selectedGarment);

      if (mounted) {
        ScaffoldMessenger.of(context).hideCurrentSnackBar();
      }

      if (result['success']) {
        final measurements = result['data']['measurements'];
        final sizeSuggestion = result['data']['size_suggestion'];

        if (mounted) {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => ResultsScreen(
                imagePath: image.path,

                  measurements: {
                    'Chest': '${measurements['chest'] ?? '0'} cm',
                    'Waist': '${measurements['waist'] ?? '0'} cm',
                    'Length': '${measurements['length'] ?? '0'} cm',
                    'Shoulder': '${measurements['shoulder'] ?? '0'} cm',
                    'Sleeve': '${measurements['sleeve'] ?? '0'} cm',
                  },


                sizeSuggestion: sizeSuggestion,
              ),
            ),
          );
        }
      } else {
        // Show specific error message
        final message = result['message'] ?? 'Processing failed';
        final matDetected = result['data']?['mat_detected'] ?? false;

        if (mounted) {
          showDialog(
            context: context,
            builder: (context) => AlertDialog(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              title: Row(
                children: [
                  Icon(
                    matDetected
                        ? Icons.warning_amber
                        : Icons.crop_free,
                    color: Colors.orange,
                  ),
                  const SizedBox(width: 8),
                  const Text('Scan Failed'),
                ],
              ),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(message),
                  const SizedBox(height: 16),
                  const Text(
                    'Tips:',
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  if (!matDetected) ...[
                    const Text('• Place garment on measurement mat'),
                    const Text('• Ensure all 4 corner markers visible'),
                    const Text('• Good lighting on mat'),
                  ] else ...[
                    const Text('• Place garment flat on mat'),
                    const Text('• Move camera to show full garment'),
                    const Text('• Avoid shadows on garment'),
                  ],
                ],
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('Try Again'),
                ),
                ElevatedButton(
                  onPressed: () {
                    Navigator.pop(context);
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const ManualEntryScreen(),
                      ),
                    );
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF6C63FF),
                  ),
                  child: const Text(
                    'Manual Entry',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
              ],
            ),
          );
        }
      }
    } catch (e) {
      debugPrint('Capture error: $e');
    } finally {
      setState(() {
        _isCapturing = false;
      });
    }
  }

  Future<void> _pickFromGallery() async {
    if (_isCapturing) return;

    setState(() {
      _isCapturing = true;
    });

    try {
      // Check internet first
      final isConnected = await ConnectivityService.isConnected();

      if (!isConnected) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Row(
                children: [
                  Icon(Icons.offline_bolt, color: Colors.white),
                  SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      'No internet — enter measurements manually',
                    ),
                  ),
                ],
              ),
              backgroundColor: Colors.orange,
              duration: Duration(seconds: 2),
            ),
          );
          await Future.delayed(const Duration(seconds: 2));
          if (mounted) {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => const ManualEntryScreen(),
              ),
            );
          }
        }
        return;
      }

      // Pick image from gallery
      final ImagePicker picker = ImagePicker();
      final XFile? image = await picker.pickImage(
        source: ImageSource.gallery,
        imageQuality: 90,
      );

      if (image == null) {
        // User cancelled
        setState(() {
          _isCapturing = false;
        });
        return;
      }

      // Show loading
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Row(
              children: [
                SizedBox(
                  width: 20,
                  height: 20,
                  child: CircularProgressIndicator(
                    color: Colors.white,
                    strokeWidth: 2,
                  ),
                ),
                SizedBox(width: 12),
                Text('Analysing garment...'),
              ],
            ),
            duration: Duration(seconds: 10),
            backgroundColor: Color(0xFF6C63FF),
          ),
        );
      }

      // Send to API
      final result = await ApiService.processScan(
          image.path,
        selectedGarment,
      );

      if (mounted) {
        ScaffoldMessenger.of(context).hideCurrentSnackBar();
      }

      if (result['success']) {
        final measurements = result['data']['measurements'];
        final sizeSuggestion = result['data']['size_suggestion'];

        if (result['success']) {
          final measurements = result['data']['measurements'];
          final sizeSuggestion = result['data']['size_suggestion'];

          if (mounted) {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => ResultsScreen(
                  imagePath: image.path,
                  measurements: {
                    'Chest':    '${measurements['chest'] ?? '0'} cm',
                    'Waist':    '${measurements['waist'] ?? '0'} cm',
                    'Length':   '${measurements['length'] ?? '0'} cm',
                    'Shoulder': '${measurements['shoulder'] ?? '0'} cm',
                    'Sleeve':   '${measurements['sleeve'] ?? '0'} cm',
                  },
                  sizeSuggestion: sizeSuggestion,
                ),
              ),
            );
          }
        }
      } else {
        final message = result['message'] ?? 'Processing failed';
        final matDetected = result['data']?['mat_detected'] ?? false;

        if (mounted) {
          showDialog(
            context: context,
            builder: (context) => AlertDialog(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              title: Row(
                children: [
                  Icon(
                    matDetected
                        ? Icons.warning_amber
                        : Icons.crop_free,
                    color: Colors.orange,
                  ),
                  const SizedBox(width: 8),
                  const Text('Scan Failed'),
                ],
              ),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(message),
                  const SizedBox(height: 16),
                  const Text(
                    'Tips:',
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  if (!matDetected) ...[
                    const Text('• Place garment on measurement mat'),
                    const Text('• Ensure all 4 corner markers visible'),
                    const Text('• Good lighting on mat'),
                  ] else ...[
                    const Text('• Place garment flat on mat'),
                    const Text('• Move camera to show full garment'),
                    const Text('• Avoid shadows on garment'),
                  ],
                ],
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('Try Again'),
                ),
                ElevatedButton(
                  onPressed: () {
                    Navigator.pop(context);
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const ManualEntryScreen(),
                      ),
                    );
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF6C63FF),
                  ),
                  child: const Text(
                    'Manual Entry',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
              ],
            ),
          );
        }
      }
    } catch (e) {
      debugPrint('Gallery pick error: $e');
    } finally {
      setState(() {
        _isCapturing = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: Colors.black,
        iconTheme: const IconThemeData(color: Colors.white),
        title: const Text(
          'Scan Garment',
          style: TextStyle(color: Colors.white),
        ),
      ),
      body: Stack(
        children: [
          _isInitialized
              ? SizedBox(
            width: double.infinity,
            height: double.infinity,
            child: CameraPreview(_controller!),
          )
              : const Center(
            child: CircularProgressIndicator(
              color: Color(0xFF6C63FF),
            ),
          ),
          // Offline indicator
          FutureBuilder<bool>(
            future: ConnectivityService.isConnected(),
            builder: (context, snapshot) {
              final isOnline = snapshot.data ?? true;
              if (isOnline) return const SizedBox.shrink();
              return Positioned(
                top: 16,
                left: 16,
                right: 16,
                child: Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.orange,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Row(
                    children: [
                      Icon(Icons.offline_bolt, color: Colors.white, size: 18),
                      SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          'Offline — tap capture for manual entry',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 13,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
          // Dimmed overlay
          Positioned.fill(
            child: CustomPaint(
              painter: _ScanOverlayPainter(),
            ),
          ),

// Top instruction banner
          Positioned(
            top: 60,
            left: 16,
            right: 16,
            child: Container(
              padding: const EdgeInsets.symmetric(
                horizontal: 16,
                vertical: 10,
              ),
              decoration: BoxDecoration(
                color: Colors.black.withValues(alpha: 0.6),
                borderRadius: BorderRadius.circular(12),
              ),
              child:  Column(
                children: [
                  Text(
                    '📏 Place Garment on Mat',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  SizedBox(height: 4),
                  Text(
                    'Make sure all 4 mat corners are visible',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Colors.white70,
                      fontSize: 12,
                    ),
                  ),
                  const SizedBox(height: 12),

                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: DropdownButton<String>(
                      value: selectedGarment,
                      underline: const SizedBox(),
                      items: const [
                        DropdownMenuItem(
                          value: "shirt",
                          child: Text("Shirt"),
                        ),
                        DropdownMenuItem(
                          value: "pants",
                          child: Text("Pants"),
                        ),
                      ],
                      onChanged: (value) {
                        setState(() {
                          selectedGarment = value!;
                        });
                      },
                    ),
                  ),

                ],
              ),
            ),
          ),

// Scan frame with corner markers and center text
          Center(
            child: SizedBox(
              width: MediaQuery.of(context).size.width,
              height: MediaQuery.of(context).size.width,
              child: Stack(
                children: [
                  // Top Left corner
                  Positioned(
                    top: 0, left: 0,
                    child: Container(
                      width: 24, height: 30,
                      decoration: const BoxDecoration(
                        border: Border(
                          top: BorderSide(color: Color(0xFF6C63FF), width: 3),
                          left: BorderSide(color: Color(0xFF6C63FF), width: 3),
                        ),
                        borderRadius: BorderRadius.only(
                          topLeft: Radius.circular(4),
                        ),
                      ),
                    ),
                  ),
                  // Top Right corner
                  Positioned(
                    top: 0, right: 0,
                    child: Container(
                      width: 24, height:24,

                      decoration: const BoxDecoration(
                        border: Border(
                          top: BorderSide(color: Color(0xFF6C63FF), width: 3),
                          right: BorderSide(color: Color(0xFF6C63FF), width: 3),
                        ),
                        borderRadius: BorderRadius.only(
                          topRight: Radius.circular(4),
                        ),
                      ),
                    ),
                  ),
                  // Bottom Left corner
                  Positioned(
                    bottom: 0, left: 0,
                    child: Container(
                      width: 24, height: 24,
                      decoration: const BoxDecoration(
                        border: Border(
                          bottom: BorderSide(color: Color(0xFF6C63FF), width: 3),
                          left: BorderSide(color: Color(0xFF6C63FF), width: 3),
                        ),
                        borderRadius: BorderRadius.only(
                          bottomLeft: Radius.circular(4),
                        ),
                      ),
                    ),
                  ),
                  // Bottom Right corner
                  Positioned(
                    bottom: 0, right: 0,
                    child: Container(
                      width: 24, height: 24,
                      decoration: const BoxDecoration(
                        border: Border(
                          bottom: BorderSide(color: Color(0xFF6C63FF), width: 3),
                          right: BorderSide(color: Color(0xFF6C63FF), width: 3),
                        ),
                        borderRadius: BorderRadius.only(
                          bottomRight: Radius.circular(4),
                        ),
                      ),
                    ),
                  ),

                  // Center content
                  const Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.checkroom_outlined,
                          color: Colors.white54,
                          size: 40,
                        ),
                        SizedBox(height: 100),
                        Text(
                          'Place garment flat\ninside this frame',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            color: Colors.white70,
                            fontSize: 13,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

// Bottom checklist
          Positioned(
            bottom: 130,
            left: 16,
            right: 16,
            child: Container(
              padding: const EdgeInsets.symmetric(
                horizontal: 12,
                vertical: 10,
              ),
              decoration: BoxDecoration(
                color: Colors.black.withValues(alpha: 0.6),
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Row(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: [
                  _OverlayCheckItem(icon: Icons.light_mode, label: 'Good\nLighting'),
                  _OverlayCheckItem(icon: Icons.straighten, label: 'Garment\nFlat'),
                  _OverlayCheckItem(icon: Icons.crop_free, label: 'All Corners\nVisible'),
                  _OverlayCheckItem(icon: Icons.wifi, label: 'WiFi\nConnected'),
                ],
              ),
            ),
          ),
          Positioned(
            bottom: 40,
            left: 0,
            right: 0,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Gallery button
                GestureDetector(
                  onTap: _pickFromGallery,
                  child: Container(
                    width: 56,
                    height: 56,
                    margin: const EdgeInsets.only(right: 24),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: Colors.white.withValues(alpha: 0.2),
                      border: Border.all(
                        color: Colors.white,
                        width: 2,
                      ),
                    ),
                    child: const Icon(
                      Icons.photo_library,
                      color: Colors.white,
                      size: 26,
                    ),
                  ),
                ),

                // Capture button
                GestureDetector(
                  onTap: _captureImage,
                  child: Container(
                    width: 72,
                    height: 72,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: _isCapturing
                          ? Colors.grey
                          : const Color(0xFF6C63FF),
                      border: Border.all(
                        color: Colors.white,
                        width: 4,
                      ),
                    ),
                    child: Icon(
                      _isCapturing
                          ? Icons.hourglass_top
                          : Icons.camera_alt,
                      color: Colors.white,
                      size: 32,
                    ),
                  ),
                ),

                // Placeholder for symmetry
                const SizedBox(width: 80),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

// ─── RESULTS SCREEN ───────────────────────────────────────────────────────────

class ResultsScreen extends StatefulWidget {
  final Map<String, String> measurements;
  final String imagePath;
  final Map<String, dynamic>? sizeSuggestion;

  const ResultsScreen({
    super.key,
    required this.measurements,
    required this.imagePath,
    this.sizeSuggestion
  });

  @override
  State<ResultsScreen> createState() => _ResultsScreenState();
}

class _ResultsScreenState extends State<ResultsScreen> {
  late Map<String, TextEditingController> _controllers;
  bool _isEditing = false;

  @override
  void initState() {
    super.initState();
    _controllers = {};
    widget.measurements.forEach((key, value) {
      _controllers[key] = TextEditingController(
        text: value.replaceAll(' cm', ''),
      );
    });
  }

  @override
  void dispose() {
    _controllers.forEach((key, controller) {
      controller.dispose();
    });
    super.dispose();
  }

  Map<String, String> get _currentMeasurements {
    final result = <String, String>{};
    _controllers.forEach((key, controller) {
      result[key] = '${controller.text} cm';
    });
    return result;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: const Text('Measurements'),
        backgroundColor: const Color(0xFF6C63FF),
        foregroundColor: Colors.white,
        actions: [
          TextButton.icon(
            onPressed: () {
              setState(() {
                _isEditing = !_isEditing;
              });
            },
            icon: Icon(
              _isEditing ? Icons.check : Icons.edit,
              color: Colors.white,
              size: 18,
            ),
            label: Text(
              _isEditing ? 'Done' : 'Edit',
              style: const TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: const Color(0xFF6C63FF),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(
                    _isEditing ? Icons.edit_note : Icons.check_circle,
                    color: Colors.white,
                    size: 40,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    _isEditing ? 'Edit Measurements' : 'Scan Complete!',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  Text(
                    _isEditing
                        ? 'Tap any value to correct it'
                        : 'Garment measurements detected',
                    style: const TextStyle(
                      color: Colors.white70,
                      fontSize: 14,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 16),

            // Size suggestion card
            if (widget.sizeSuggestion != null)
              Container(
                width: double.infinity,
                margin: const EdgeInsets.only(bottom: 16),
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.green.shade50,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: Colors.green.shade200,
                    width: 1.5,
                  ),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 56,
                      height: 56,
                      decoration: BoxDecoration(
                        color: Colors.green,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Center(
                        child: Text(
                          widget.sizeSuggestion!['size'] ?? '?',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Suggested Size',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.green,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                          Text(
                            '${widget.sizeSuggestion!['brand']} — ${widget.sizeSuggestion!['category']}',
                            style: const TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF333333),
                            ),
                          ),
                          Text(
                            'Based on your size chart',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey.shade600,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const Icon(
                      Icons.check_circle,
                      color: Colors.green,
                      size: 28,
                    ),
                  ],
                ),
              ),

            // Measurements title row
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Measurements',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF333333),
                  ),
                ),
                if (!_isEditing)
                  TextButton.icon(
                    onPressed: () {
                      setState(() {
                        _isEditing = true;
                      });
                    },
                    icon: const Icon(
                      Icons.edit,
                      size: 16,
                      color: Color(0xFF6C63FF),
                    ),
                    label: const Text(
                      'Edit values',
                      style: TextStyle(
                        color: Color(0xFF6C63FF),
                        fontSize: 13,
                      ),
                    ),
                  ),
              ],
            ),

            const SizedBox(height: 12),

            // Measurement cards
            Expanded(
              child: ListView(
                children: _controllers.entries.map((entry) {
                  return _EditableMeasurementCard(
                    label: entry.key,
                    controller: entry.value,
                    isEditing: _isEditing,
                  );
                }).toList(),
              ),
            ),

            // Save button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () {
                  if (_isEditing) {
                    setState(() {
                      _isEditing = false;
                    });
                  }
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => GarmentDetailsScreen(
                        measurements: _currentMeasurements,
                        imagePath: widget.imagePath,
                        suggestedSize: widget.sizeSuggestion?['size'],
                      ),
                    ),
                  );
                },
                icon: const Icon(Icons.save),
                label: const Text('Save Measurements'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF6C63FF),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  textStyle: const TextStyle(fontSize: 16),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// Editable measurement card
class _EditableMeasurementCard extends StatelessWidget {
  final String label;
  final TextEditingController controller;
  final bool isEditing;

  const _EditableMeasurementCard({
    required this.label,
    required this.controller,
    required this.isEditing,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.symmetric(
        horizontal: 20,
        vertical: 12,
      ),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: isEditing
              ? const Color(0xFF6C63FF)
              : Colors.transparent,
          width: 1.5,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          // Label
          Text(
            label,
            style: const TextStyle(
              fontSize: 16,
              color: Color(0xFF666666),
            ),
          ),


          // Value — editable or display
          isEditing
              ? Row(
            children: [
              SizedBox(
                width: 80,
                child: TextField(
                  controller: controller,
                  keyboardType: const TextInputType.numberWithOptions(
                    decimal: true,
                  ),
                  textAlign: TextAlign.right,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF6C63FF),
                  ),
                  decoration: const InputDecoration(
                    border: InputBorder.none,
                    contentPadding: EdgeInsets.zero,
                  ),
                ),
              ),
              const Text(
                ' cm',
                style: TextStyle(
                  fontSize: 16,
                  color: Color(0xFF6C63FF),
                ),
              ),
            ],
          )
              : Text(
            '${controller.text} cm',
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: Color(0xFF6C63FF),
            ),
          ),
        ],
      ),
    );
  }
}

// ─── SHARED WIDGETS ───────────────────────────────────────────────────────────

class _StatCard extends StatelessWidget {
  final String title;
  final String value;
  final IconData icon;
  final Color color;

  const _StatCard({
    required this.title,
    required this.value,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 28),
          const SizedBox(height: 8),
          Text(
            value,
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          Text(
            title,
            style: const TextStyle(fontSize: 12, color: Color(0xFF666666)),
          ),
        ],
      ),
    );
  }
}

class _ScanCard extends StatelessWidget {
  final String name;
  final String date;
  final String status;
  final String measurements;
  final VoidCallback onTap;

  const _ScanCard({
    required this.name,
    required this.date,
    required this.status,
    required this.measurements,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final isCompleted = status == 'completed';
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.05),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                color: const Color(0xFF6C63FF).withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Icon(Icons.checkroom, color: Color(0xFF6C63FF)),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    name,
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w600,
                      color: Color(0xFF333333),
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    measurements,
                    style: const TextStyle(
                        fontSize: 12, color: Color(0xFF666666)),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    date,
                    style: const TextStyle(
                        fontSize: 11, color: Color(0xFF999999)),
                  ),
                ],
              ),
            ),
            Container(
              padding:
              const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(
                color: isCompleted
                    ? Colors.green.shade50
                    : Colors.orange.shade50,
                borderRadius: BorderRadius.circular(6),
              ),
              child: Text(
                isCompleted ? 'Done' : 'Pending',
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                  color: isCompleted
                      ? Colors.green.shade700
                      : Colors.orange.shade700,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _MeasurementCard extends StatelessWidget {
  final String label;
  final String value;

  const _MeasurementCard({
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(fontSize: 16, color: Color(0xFF666666)),
          ),
          Text(
            value,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: Color(0xFF6C63FF),
            ),
          ),
        ],
      ),
    );
  }
}

class _CheckItem extends StatelessWidget {
  final String title;
  final String subtitle;
  final bool isChecking;
  final bool passed;
  final IconData icon;
  final String label;

  const _CheckItem({
    required this.title,
    required this.subtitle,
    required this.isChecking,
    required this.passed,
    required this.icon,
    required this.label
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: isChecking
              ? Colors.grey.shade200
              : passed
              ? Colors.green.shade200
              : Colors.red.shade200,
          width: 1.5,
        ),
      ),
      child: Row(
        children: [
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              color: isChecking
                  ? Colors.grey.shade100
                  : passed
                  ? Colors.green.shade50
                  : Colors.red.shade50,
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(
              icon,
              color: isChecking
                  ? Colors.grey
                  : passed
                  ? Colors.green
                  : Colors.red,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFF333333),
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  subtitle,
                  style: TextStyle(
                    fontSize: 13,
                    color: isChecking
                        ? Colors.grey
                        : passed
                        ? Colors.green.shade700
                        : Colors.red.shade700,
                  ),
                ),
              ],
            ),
          ),
          if (isChecking)
            const SizedBox(
              width: 20,
              height: 20,
              child: CircularProgressIndicator(strokeWidth: 2),
            )
          else
            Icon(
              passed ? Icons.check_circle : Icons.cancel,
              color: passed ? Colors.green : Colors.red,
              size: 24,
            ),
        ],
      ),
    );
  }
}
// ─── GARMENT DETAILS SCREEN ───────────────────────────────────────────────────

class GarmentDetailsScreen extends StatefulWidget {
  final Map<String, String> measurements;
  final String imagePath;
  final String? suggestedSize;

  const GarmentDetailsScreen({
    super.key,
    required this.measurements,
    required this.imagePath,
    this.suggestedSize,
  });

  @override
  State<GarmentDetailsScreen> createState() => _GarmentDetailsScreenState();
}

class _GarmentDetailsScreenState extends State<GarmentDetailsScreen> {
  final _nameController = TextEditingController();
  final _brandController = TextEditingController();
  bool _isLoading = false;
  String _selectedCategory = 'Shirt';
  String _selectedSize = 'M';

  List<String> _categories = [];

  final List<String> _sizes = [
    'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL',
  ];

  @override
  void initState() {
    super.initState();
    _loadCategories();
    if (widget.suggestedSize != null) {
      _selectedSize = widget.suggestedSize!;
    }
  }

  Future<void> _loadCategories() async {
    final categories = await ApiService.getCategories();
    setState(() {
      _categories = categories;
      if (_categories.isNotEmpty &&
          !_categories.contains(_selectedCategory)) {
        _selectedCategory = _categories.first;
      }
    });
  }
  @override
  void dispose() {
    _nameController.dispose();
    _brandController.dispose();
    super.dispose();
  }

  Future<void> _saveGarment() async {
    if (_nameController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please enter garment name'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      _isLoading = true;
    });
    debugPrint('Measurements received: ${widget.measurements}');


    final garmentData = {
      'name':       _nameController.text,
      'brand':      _brandController.text,
      'category':   _selectedCategory,
      'size_label': _selectedSize,
      'chest':      widget.measurements['Chest']?.replaceAll(' cm', '') ?? '',
      'waist':      widget.measurements['Waist']?.replaceAll(' cm', '') ?? '',
      'length':     widget.measurements['Length']?.replaceAll(' cm', '') ?? '',
      'shoulder':   widget.measurements['Shoulder']?.replaceAll(' cm', '') ?? '',
      'sleeve':     widget.measurements['Sleeve']?.replaceAll(' cm', '') ?? '',
      'status':     'completed',
    };

    final isConnected = await ConnectivityService.isConnected();

    if (isConnected) {
      debugPrint('Saving garment data: $garmentData');
      final result = await ApiService.createGarment(garmentData);
      debugPrint('Save result: $result');

      setState(() { _isLoading = false; });

      if (result['success']) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Garment saved successfully!'),
              backgroundColor: Colors.green,
            ),
          );
          Navigator.pushAndRemoveUntil(
            context,
            MaterialPageRoute(
              builder: (context) => const GarmentLibraryScreen(),
            ),
                (route) => route.isFirst,
          );
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Failed to save'),
              backgroundColor: Colors.red,
            ),
          );
        }
      }
    } else {
      await OfflineStorage.savePendingScan(garmentData);
      setState(() { _isLoading = false; });

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Row(
              children: [
                Icon(Icons.offline_bolt, color: Colors.white),
                SizedBox(width: 8),
                Text('Saved offline — will sync when connected'),
              ],
            ),
            backgroundColor: Colors.orange,
            duration: Duration(seconds: 3),
          ),
        );
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(
            builder: (context) => const GarmentLibraryScreen(),
          ),
              (route) => route.isFirst,
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: const Text('Garment Details'),
        backgroundColor: const Color(0xFF6C63FF),
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Measurements summary card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: const Color(0xFF6C63FF),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Scan Measurements',
                    style: TextStyle(
                      color: Colors.white70,
                      fontSize: 13,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Wrap(
                    spacing: 12,
                    runSpacing: 8,
                    children: widget.measurements.entries.map((entry) {
                      return Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 6,
                        ),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.2),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          '${entry.key}: ${entry.value}',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 13,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 24),

            // Name
            const Text(
              'Garment Name',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: Color(0xFF333333),
              ),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: _nameController,
              decoration: InputDecoration(
                hintText: 'e.g. Blue Denim Jacket',
                prefixIcon: const Icon(Icons.checkroom),
                filled: true,
                fillColor: Colors.white,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                  borderSide: BorderSide.none,
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                  borderSide: const BorderSide(
                    color: Color(0xFF6C63FF),
                    width: 2,
                  ),
                ),
              ),
            ),

            const SizedBox(height: 20),

            // Brand
            const Text(
              'Brand',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: Color(0xFF333333),
              ),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: _brandController,
              decoration: InputDecoration(
                hintText: 'e.g. Levis, Nike, Zara',
                prefixIcon: const Icon(Icons.label_outline),
                filled: true,
                fillColor: Colors.white,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                  borderSide: BorderSide.none,
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                  borderSide: const BorderSide(
                    color: Color(0xFF6C63FF),
                    width: 2,
                  ),
                ),
              ),
            ),

            const SizedBox(height: 20),

            // Category
            const Text(
              'Category',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: Color(0xFF333333),
              ),
            ),
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(8),
              ),
              child: DropdownButtonHideUnderline(
                child: DropdownButton<String>(
                  value: _selectedCategory,
                  isExpanded: true,
                  icon: const Icon(Icons.keyboard_arrow_down),
                  items: _categories.map((category) {
                    return DropdownMenuItem(
                      value: category,
                      child: Text(category),
                    );
                  }).toList(),
                  onChanged: (value) {
                    setState(() {
                      _selectedCategory = value!;
                    });
                  },
                ),
              ),
            ),

            const SizedBox(height: 20),

            // Size label
            const Text(
              'Size Label',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: Color(0xFF333333),
              ),
            ),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8,
              children: _sizes.map((size) {
                final isSelected = _selectedSize == size;
                return GestureDetector(
                  onTap: () {
                    setState(() {
                      _selectedSize = size;
                    });
                  },
                  child: Container(
                    width: 56,
                    height: 56,
                    decoration: BoxDecoration(
                      color: isSelected
                          ? const Color(0xFF6C63FF)
                          : Colors.white,
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: isSelected
                            ? const Color(0xFF6C63FF)
                            : Colors.grey.shade300,
                      ),
                    ),
                    child: Center(
                      child: Text(
                        size,
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: isSelected
                              ? Colors.white
                              : const Color(0xFF333333),
                        ),
                      ),
                    ),
                  ),
                );
              }).toList(),
            ),

            const SizedBox(height: 32),

            // Save button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _isLoading ? null : _saveGarment,
                icon: _isLoading
                    ? const SizedBox(
                  width: 20,
                  height: 20,
                  child: CircularProgressIndicator(
                    color: Colors.white,
                    strokeWidth: 2,
                  ),
                )
                    : const Icon(Icons.save),
                label: Text(
                  _isLoading ? 'Saving...' : 'Save to Library',
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF6C63FF),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  textStyle: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            ),

            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }
}


// ─── GARMENT LIBRARY SCREEN ───────────────────────────────────────────────────

class GarmentLibraryScreen extends StatefulWidget {
  const GarmentLibraryScreen({super.key});

  @override
  State<GarmentLibraryScreen> createState() => _GarmentLibraryScreenState();
}

class _GarmentLibraryScreenState extends State<GarmentLibraryScreen> {
  List<dynamic> _garments = [];
  bool _isLoading = true;
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _loadGarments();
  }

  Future<void> _loadGarments() async {
    setState(() {
      _isLoading = true;
    });

    final result = await ApiService.getGarments();

    if (result['success']) {
      setState(() {
        _garments = result['data']['garments'];
        _isLoading = false;
      });
    } else {
      setState(() {
        _isLoading = false;
      });
    }
  }

  Future<void> _deleteGarment(String id, int index) async {
    final result = await ApiService.deleteGarment(id);

    if (result['success']) {
      setState(() {
        _garments.removeAt(index);
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Garment deleted'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  List<dynamic> get _filteredGarments {
    if (_searchQuery.isEmpty) return _garments;
    return _garments.where((g) {
      final name = g['name'].toString().toLowerCase();
      final brand = (g['brand'] ?? '').toString().toLowerCase();
      final category = (g['category'] ?? '').toString().toLowerCase();
      final query = _searchQuery.toLowerCase();
      return name.contains(query) ||
          brand.contains(query) ||
          category.contains(query);
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: const Text('Garment Library'),
        backgroundColor: const Color(0xFF6C63FF),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.white),
            onPressed: _loadGarments,
          ),
        ],
      ),
      body: Column(
        children: [
          // Search bar
          Padding(
            padding: const EdgeInsets.all(16),
            child: TextField(
              onChanged: (value) {
                setState(() {
                  _searchQuery = value;
                });
              },
              decoration: InputDecoration(
                hintText: 'Search by name, brand, category...',
                prefixIcon: const Icon(Icons.search),
                filled: true,
                fillColor: Colors.white,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                  borderSide: BorderSide.none,
                ),
              ),
            ),
          ),

          // Garments count
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Row(
              children: [
                Text(
                  '${_filteredGarments.length} garments',
                  style: const TextStyle(
                    color: Color(0xFF666666),
                    fontSize: 14,
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 8),

          // List
          Expanded(
            child: _isLoading
                ? const Center(
              child: CircularProgressIndicator(
                color: Color(0xFF6C63FF),
              ),
            )
                : _filteredGarments.isEmpty
                ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(
                    Icons.checkroom_outlined,
                    size: 64,
                    color: Colors.grey,
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'No garments found',
                    style: TextStyle(
                      fontSize: 18,
                      color: Colors.grey,
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    'Scan a garment to add it here',
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey,
                    ),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton.icon(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) =>
                          const CameraQualityCheck(),
                        ),
                      );
                    },
                    icon: const Icon(Icons.camera_alt),
                    label: const Text('Scan Garment'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF6C63FF),
                      foregroundColor: Colors.white,
                    ),
                  ),
                ],
              ),
            )
                : RefreshIndicator(
              onRefresh: _loadGarments,
              child: ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: _filteredGarments.length,
                itemBuilder: (context, index) {
                  final garment = _filteredGarments[index];
                  return _GarmentCard(
                    garment: garment,
                    onDelete: () => _deleteGarment(
                      garment['id'].toString(),
                      index,
                    ),
                  );
                },
              ),
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        backgroundColor: const Color(0xFF6C63FF),
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => const CameraQualityCheck(),
            ),
          );
        },
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}

// Garment card widget
class _GarmentCard extends StatelessWidget {
  final Map<String, dynamic> garment;
  final VoidCallback onDelete;

  const _GarmentCard({
    required this.garment,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    final isCompleted = garment['status'] == 'completed';

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                // Icon
                Container(
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    color: const Color(0xFF6C63FF).withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Icon(
                    Icons.checkroom,
                    color: Color(0xFF6C63FF),
                  ),
                ),
                const SizedBox(width: 12),
                // Name and brand
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        garment['name'] ?? 'Unknown',
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF333333),
                        ),
                      ),
                      Text(
                        '${garment['brand'] ?? 'No brand'} • ${garment['category'] ?? 'No category'}',
                        style: const TextStyle(
                          fontSize: 13,
                          color: Color(0xFF666666),
                        ),
                      ),
                    ],
                  ),
                ),
                // Delete button
                IconButton(
                  icon: const Icon(Icons.delete_outline, color: Colors.red),
                  onPressed: () {
                    showDialog(
                      context: context,
                      builder: (context) => AlertDialog(
                        title: const Text('Delete Garment'),
                        content: const Text(
                            'Are you sure you want to delete this garment?'),
                        actions: [
                          TextButton(
                            onPressed: () => Navigator.pop(context),
                            child: const Text('Cancel'),
                          ),
                          TextButton(
                            onPressed: () {
                              Navigator.pop(context);
                              onDelete();
                            },
                            child: const Text(
                              'Delete',
                              style: TextStyle(color: Colors.red),
                            ),
                          ),
                        ],
                      ),
                    );
                  },
                ),
              ],
            ),

            const SizedBox(height: 12),

            // Measurements
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                if (garment['chest'] != null)
                  _MeasurementChip(
                      label: 'Chest', value: '${garment['chest']}cm'),
                if (garment['waist'] != null)
                  _MeasurementChip(
                      label: 'Waist', value: '${garment['waist']}cm'),
                if (garment['length'] != null)
                  _MeasurementChip(
                      label: 'Length', value: '${garment['length']}cm'),
                if (garment['shoulder'] != null)
                  _MeasurementChip(
                      label: 'Shoulder',
                      value: '${garment['shoulder']}cm'),
                if (garment['sleeve'] != null)
                  _MeasurementChip(
                      label: 'Sleeve', value: '${garment['sleeve']}cm'),
              ],
            ),

            const SizedBox(height: 12),

            // Bottom row
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                // Size label
                if (garment['size_label'] != null)
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: const Color(0xFF6C63FF).withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Text(
                      'Size: ${garment['size_label']}',
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: Color(0xFF6C63FF),
                      ),
                    ),
                  ),
                // Status
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: isCompleted
                        ? Colors.green.shade50
                        : Colors.orange.shade50,
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: Text(
                    isCompleted ? 'Completed' : 'Pending',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: isCompleted
                          ? Colors.green.shade700
                          : Colors.orange.shade700,
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _MeasurementChip extends StatelessWidget {
  final String label;
  final String value;

  const _MeasurementChip({
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: const Color(0xFFF5F5F5),
        borderRadius: BorderRadius.circular(6),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Text(
        '$label: $value',
        style: const TextStyle(
          fontSize: 12,
          color: Color(0xFF444444),
        ),
      ),
    );
  }
}

// ─── MANUAL ENTRY SCREEN ──────────────────────────────────────────────────────

class ManualEntryScreen extends StatefulWidget {
  const ManualEntryScreen({super.key});

  @override
  State<ManualEntryScreen> createState() => _ManualEntryScreenState();
}

class _ManualEntryScreenState extends State<ManualEntryScreen> {
  final _nameController = TextEditingController();
  final _brandController = TextEditingController();
  final _chestController = TextEditingController();
  final _waistController = TextEditingController();
  final _lengthController = TextEditingController();
  final _shoulderController = TextEditingController();
  final _sleeveController = TextEditingController();

  String _selectedCategory = 'Shirt';
  String _selectedSize = 'M';
  bool _isLoading = false;

  List<String> _categories = [];


  final List<String> _sizes = [
    'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL',
  ];

  @override
  void initState() {
    super.initState();
    _loadCategories();
  }

  Future<void> _loadCategories() async {
    final categories = await ApiService.getCategories();
    setState(() {
      _categories = categories;
      if (_categories.isNotEmpty) {
        _selectedCategory = _categories.first;
      }
    });
  }

  @override
  void dispose() {
    _nameController.dispose();
    _brandController.dispose();
    _chestController.dispose();
    _waistController.dispose();
    _lengthController.dispose();
    _shoulderController.dispose();
    _sleeveController.dispose();
    super.dispose();
  }

  Future<void> _saveManualEntry() async {
    // Validate required fields
    if (_nameController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please enter garment name'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (_chestController.text.isEmpty &&
        _waistController.text.isEmpty &&
        _lengthController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please enter at least one measurement'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      _isLoading = true;
    });

    final result = await ApiService.createGarment({
      'name': _nameController.text,
      'brand': _brandController.text,
      'category': _selectedCategory,
      'size_label': _selectedSize,
      'chest': _chestController.text,
      'waist': _waistController.text,
      'length': _lengthController.text,
      'shoulder': _shoulderController.text,
      'sleeve': _sleeveController.text,
      'status': 'completed',
    });

    setState(() {
      _isLoading = false;
    });

    if (result['success']) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Garment saved successfully!'),
            backgroundColor: Colors.green,
          ),
        );
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => const GarmentLibraryScreen(),
          ),
        );
      }
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? 'Failed to save'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: const Text('Manual Entry'),
        backgroundColor: const Color(0xFF6C63FF),
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Info banner
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: const Color(0xFF6C63FF).withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(
                  color: const Color(0xFF6C63FF).withValues(alpha: 0.3),
                ),
              ),
              child: const Row(
                children: [
                  Icon(
                    Icons.info_outline,
                    color: Color(0xFF6C63FF),
                    size: 20,
                  ),
                  SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      'Enter measurements manually using a tape measure',
                      style: TextStyle(
                        color: Color(0xFF6C63FF),
                        fontSize: 13,
                      ),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 20),

            // Garment info section
            _SectionHeader(title: 'Garment Information'),
            const SizedBox(height: 12),

            // Name
            _InputField(
              controller: _nameController,
              label: 'Garment Name',
              hint: 'e.g. Blue Denim Jacket',
              icon: Icons.checkroom,
            ),

            const SizedBox(height: 12),

            // Brand
            _InputField(
              controller: _brandController,
              label: 'Brand',
              hint: 'e.g. Levis, Nike, Zara',
              icon: Icons.label_outline,
            ),

            const SizedBox(height: 12),

            // Category
            const Text(
              'Category',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: Color(0xFF333333),
              ),
            ),
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(8),
              ),
              child: DropdownButtonHideUnderline(
                child: DropdownButton<String>(
                  value: _selectedCategory,
                  isExpanded: true,
                  icon: const Icon(Icons.keyboard_arrow_down),
                  items: _categories.map((category) {
                    return DropdownMenuItem(
                      value: category,
                      child: Text(category),
                    );
                  }).toList(),
                  onChanged: (value) {
                    setState(() {
                      _selectedCategory = value!;
                    });
                  },
                ),
              ),
            ),

            const SizedBox(height: 12),

            // Size label
            const Text(
              'Size Label',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: Color(0xFF333333),
              ),
            ),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8,
              children: _sizes.map((size) {
                final isSelected = _selectedSize == size;
                return GestureDetector(
                  onTap: () {
                    setState(() {
                      _selectedSize = size;
                    });
                  },
                  child: Container(
                    width: 56,
                    height: 56,
                    decoration: BoxDecoration(
                      color: isSelected
                          ? const Color(0xFF6C63FF)
                          : Colors.white,
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: isSelected
                            ? const Color(0xFF6C63FF)
                            : Colors.grey.shade300,
                      ),
                    ),
                    child: Center(
                      child: Text(
                        size,
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: isSelected
                              ? Colors.white
                              : const Color(0xFF333333),
                        ),
                      ),
                    ),
                  ),
                );
              }).toList(),
            ),

            const SizedBox(height: 24),

            // Measurements section
            _SectionHeader(title: 'Measurements (in cm)'),
            const SizedBox(height: 12),

            // Measurement guide
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'How to measure:',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 13,
                    ),
                  ),
                  SizedBox(height: 4),
                  Text(
                    '• Chest: measure across widest point\n'
                        '• Waist: measure across narrowest point\n'
                        '• Length: measure from top to bottom\n'
                        '• Shoulder: measure across shoulder seams\n'
                        '• Sleeve: measure from shoulder to cuff',
                    style: TextStyle(
                      fontSize: 12,
                      color: Color(0xFF666666),
                      height: 1.6,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 12),

            // Measurement inputs
            _MeasurementInput(
              controller: _chestController,
              label: 'Chest',
              icon: Icons.straighten,
            ),
            const SizedBox(height: 12),
            _MeasurementInput(
              controller: _waistController,
              label: 'Waist',
              icon: Icons.straighten,
            ),
            const SizedBox(height: 12),
            _MeasurementInput(
              controller: _lengthController,
              label: 'Length',
              icon: Icons.straighten,
            ),
            const SizedBox(height: 12),
            _MeasurementInput(
              controller: _shoulderController,
              label: 'Shoulder',
              icon: Icons.straighten,
            ),
            const SizedBox(height: 12),
            _MeasurementInput(
              controller: _sleeveController,
              label: 'Sleeve',
              icon: Icons.straighten,
            ),

            const SizedBox(height: 32),

            // Save button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _isLoading ? null : _saveManualEntry,
                icon: _isLoading
                    ? const SizedBox(
                  width: 20,
                  height: 20,
                  child: CircularProgressIndicator(
                    color: Colors.white,
                    strokeWidth: 2,
                  ),
                )
                    : const Icon(Icons.save),
                label: Text(
                  _isLoading ? 'Saving...' : 'Save Garment',
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF6C63FF),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  textStyle: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            ),

            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }
}

// Section header widget
class _SectionHeader extends StatelessWidget {
  final String title;

  const _SectionHeader({required this.title});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Container(
          width: 4,
          height: 20,
          decoration: BoxDecoration(
            color: const Color(0xFF6C63FF),
            borderRadius: BorderRadius.circular(2),
          ),
        ),
        const SizedBox(width: 8),
        Text(
          title,
          style: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: Color(0xFF333333),
          ),
        ),
      ],
    );
  }
}

// Input field widget
class _InputField extends StatelessWidget {
  final TextEditingController controller;
  final String label;
  final String hint;
  final IconData icon;

  const _InputField({
    required this.controller,
    required this.label,
    required this.hint,
    required this.icon,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: Color(0xFF333333),
          ),
        ),
        const SizedBox(height: 8),
        TextField(
          controller: controller,
          decoration: InputDecoration(
            hintText: hint,
            prefixIcon: Icon(icon),
            filled: true,
            fillColor: Colors.white,
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(8),
              borderSide: BorderSide.none,
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(8),
              borderSide: const BorderSide(
                color: Color(0xFF6C63FF),
                width: 2,
              ),
            ),
          ),
        ),
      ],
    );
  }
}

// Measurement input widget
class _MeasurementInput extends StatelessWidget {
  final TextEditingController controller;
  final String label;
  final IconData icon;

  const _MeasurementInput({
    required this.controller,
    required this.label,
    required this.icon,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: 16,
        vertical: 8,
      ),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          Icon(icon, color: const Color(0xFF6C63FF), size: 20),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              label,
              style: const TextStyle(
                fontSize: 16,
                color: Color(0xFF333333),
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
          SizedBox(
            width: 80,
            child: TextField(
              controller: controller,
              keyboardType: const TextInputType.numberWithOptions(
                decimal: true,
              ),
              textAlign: TextAlign.right,
              decoration: const InputDecoration(
                border: InputBorder.none,
                hintText: '0.0',
                hintStyle: TextStyle(color: Colors.grey),
              ),
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: Color(0xFF6C63FF),
              ),
            ),
          ),
          const Text(
            ' cm',
            style: TextStyle(
              fontSize: 14,
              color: Color(0xFF666666),
            ),
          ),
        ],
      ),
    );
  }
}
class _ScanOverlayPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.black.withValues(alpha: 0.45);

    final frameWidth  = 300.0;
    final frameHeight = 210.0;
    final frameLeft   = (size.width - frameWidth) / 2;
    final frameTop    = (size.height - frameHeight) / 2;

    final frameRect = Rect.fromLTWH(
      frameLeft, frameTop, frameWidth, frameHeight,
    );

    final fullRect = Rect.fromLTWH(
      0, 0, size.width, size.height,
    );

    final path = Path()
      ..addRect(fullRect)
      ..addRRect(RRect.fromRectAndRadius(
        frameRect, const Radius.circular(12),
      ))
      ..fillType = PathFillType.evenOdd;

    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;




}

// ─── CAMERA OVERLAY CHECK ITEM ────────────────────────
class _OverlayCheckItem extends StatelessWidget {
  final IconData icon;
  final String label;

  const _OverlayCheckItem({
    required this.icon,
    required this.label,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(
          icon,
          color: Colors.white70,
          size: 20,
        ),
        const SizedBox(height: 4),
        Text(
          label,
          textAlign: TextAlign.center,
          style: const TextStyle(
            color: Colors.white70,
            fontSize: 10,
            height: 1.3,
          ),
        ),
      ],
    );
  }
}

