use Psr\Log\LoggerInterface;

final class ErrorBootstrapper
{
    public function __construct(
        private EnvironmentDetector $detector,
        private RendererFactory      $rendererFactory,
        private LoggerInterface      $logger,
        private bool                 $debug
    ) {}

    public function register(): void
    {
        $env      = $this->detector->detect();
        $renderer = $this->rendererFactory->make($env, $this->debug);

        // PHP error handler
        (new PhpErrorHandler($this->logger, $this->getIgnoredLevels()))
            ->register();

        // Exception + shutdown
        (new ExceptionHandler($renderer, $this->logger))->register();
        (new ShutdownHandler($renderer, $this->logger))->register();
    }

    private function getIgnoredLevels(): array
    {
        return $this->debug ? [] : [E_DEPRECATED, E_USER_DEPRECATED];
    }
}